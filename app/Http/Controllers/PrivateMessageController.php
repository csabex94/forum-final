<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\PrivateMessage;
use App\Models\PrivateRead;
use Illuminate\Http\Request;
use App\Events\NewPrivateMessageEvent;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;

class PrivateMessageController extends Controller
{
    /**
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        if (!$request->file('files')) {
            $request->validate([
                'message' => 'required|string'
            ]);
        }

        $message = new PrivateMessage();

        $message->user_id = $request->user()->id;
        $message->team_id = $request->user()->current_team_id;
        $message->read = false;
        $message->message = $request->message;

        $myTeam = $request->user()->currentTeam;

        if ($myTeam->is_deleted && $myTeam->user_id_deleted && $request->user()->id !== $myTeam->user_id_deleted) {
            return response()->json([
                'newPrivateMessage' => NULL,
                'error' => true
            ], 200);
        } else {
            $message->save();
            if ($request->file('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = basename($file->store('public'));
                    $originalFilename = $file->getClientOriginalName();
                    $fileSize = $file->getSize();
                    $ext = $file->getClientOriginalExtension();


                    $file = new File();
                    $file->private_message_id = $message->id;
                    $file->user_id = request()->user()->id;
                    $file->filename = $filename;
                    $file->original_filename = $originalFilename;
                    $file->size = $fileSize;
                    $file->team_id = $request->user()->current_team_id;
                    $file->extension = $ext;
                    $file->thumbnail = 'thumbnail_'.$filename;
                    $file->save();

                    $user = $request->user();

                    activity()
                        ->causedBy($request->user())
                        ->performedOn($myTeam)
                        ->tap(function(Activity $activity) use ($myTeam, $user, $file) {
                            $activity->subject_id = $myTeam->id;
                            $activity->causer_id = $user->id;
                            $activity->log_name = 'created';
                            $activity->description = 'Uploaded File #'. $file->id;
                        })
                        ->log('created');
                }
            }
            $message->load('files');
            if (!$message->message && count($message->files) > 0) {
                $myTeam->last_message = $message->files[count($message->files) - 1]->original_filename;
            } else {
                $myTeam->last_message = $message->message;
            }

            $myTeam->save();

            $teamUsers = $request->user()->currentTeam->users;
            $teamUsers->push($request->user()->currentTeam->owner);
            foreach($teamUsers as $user) {
                if ($message->user_id !== $user->id) {
                    event(new NewPrivateMessageEvent($message, $user->id));
                    PrivateRead::create([
                        'user_id' => $user->id,
                        'private_message_id' => $message->id
                    ]);
                }
            }

            return response()->json([
                'groupFiles' => $this->getGroupFiles($request),
                'groupMedia' => $this->getGroupMedia($request),
                'newPrivateMessage' => $message->load('createdBy', 'seenBy', 'files'),
                'tempUid' => $request->tempUid ?: NULL
            ], 200);
        }
    }

    public function deletePrivate(Request $request): \Illuminate\Http\JsonResponse
    {
        $message = PrivateMessage::where('id', $request->pmid)->first();
        $message->is_deleted = 1;
        $message->save();
        return response()->json('', 200);
    }

    public function deletePrivatePermanently(Request $request): \Illuminate\Http\JsonResponse
    {
        $message = PrivateMessage::where('id', $request->pmid)->first();
        if ($message && $message->is_deleted) {
            $message->delete();
        }
        return response()->json(['pmid' => $request->pmid], 200);
    }

    public function getGroupFiles($request)
    {
        return File::where([['team_id', $request->user()->current_team_id], ['extension', 'pdf']])
            ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'xlsx']])
            ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'doc']])
            ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'rtf']])
            ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'txt']])
            ->get();
    }

    public function getGroupMedia($request)
    {
        return File::where([['team_id', $request->user()->current_team_id], ['extension', 'jpg']])
            ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'png']])
            ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jpeg']])
            ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jfif']])
            ->get();
    }
}
