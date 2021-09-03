<?php

namespace App\Http\Controllers;

use App\Events\NewTopicConversationEvent;
use App\Models\TopicFile;
use App\Models\Topic;
use App\Models\TopicConversation;
use App\Models\TopicRead;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class TopicConversationController extends Controller
{
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {

        if (!$request->file('files')) {
            $request->validate([
                'message' => 'required|string',
                'topicId' => 'required|integer'
            ]);
        } else {
            $request->validate([
                'topicId' => 'required|integer'
            ]);
        }

        $currentTopic = NULL;
        if (isset($request->topicId)) {
            $currentTopic = Topic::where('id', $request->topicId)->first();
        }

        if ($currentTopic) {

            $message = new TopicConversation();
            $message->message = $request->message;
            $message->topic_id = $request->topicId;
            $message->user_id = $request->user()->id;
            $message->read = false;
            $message->team_id = $request->user()->current_team_id;

            $message->save();

            $myTeam = $request->user()->currentTeam;


            $teamUsers = $request->user()->currentTeam->users;
            $teamUsers->push($request->user()->currentTeam->owner);

            if ($request->file('files')) {
                foreach ($request->file('files') as $file) {
                    $filename = basename($file->store('public'));
                    $originalFilename = $file->getClientOriginalName();
                    $fileSize = $file->getSize();
                    $ext = $file->getClientOriginalExtension();


                    $file = new TopicFile();
                    $file->user_id = request()->user()->id;
                    $file->filename = $filename;
                    $file->original_filename = $originalFilename;
                    $file->size = $fileSize;
                    $file->topic_conversation_id = $message->id;
                    $file->topic_id = $request->topicId;
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
                            $activity->description = 'Uploaded Topic File #'. $file->id;
                        })
                        ->log('created');
                }
            }

            $message->load('files');
            if (!$message->message && count($message->files) > 0) {
                $myTeam->last_message = $request->user()->name.":"." ".$message->files[count($message->files) - 1]->original_filename;
            } else {
                $myTeam->last_message = $request->user()->name.":"." ".$message->message;
            }
            $myTeam->save();

            foreach($teamUsers as $user) {
                if ($message->user_id !== $user->id) {
                    event(new NewTopicConversationEvent($message, $user->id, $message->topic_id, $currentTopic));
                    TopicRead::create([
                        'user_id' => $user->id,
                        'topic_conversation_id' => $message->id,
                        'topic_id' => $currentTopic->id
                    ]);
                }
            }

            return response()->json([
                'newTopicConversation' => $message->load(['files', 'createdBy', 'seenBy']),
                'middleSection' => 'topic-conversations',
                'currentTopics' => Topic::where('team_id', $request->user()->current_team_id)->with('createdBy')->orderBy('created_at', 'DESC')->get(),
                'groupFiles' => function () use ($request) {
                    return TopicFile::where([['team_id', $request->user()->current_team_id], ['extension', 'pdf']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'xlsx']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'doc']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'rtf']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'txt']])
                        ->get();
                },
                'groupMedia' => function () use ($request) {
                    return TopicFile::where([['team_id', $request->user()->current_team_id], ['extension', 'jpg']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'png']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jpeg']])
                        ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jfif']])
                        ->get();
                },
                'topicName' => $request->topicName ?: "",
                'currentTopic' => $currentTopic,
                'tempUid' => $request->tempUid ?: NULL
            ], 200);
        }
        return response()->json([
            'middleSection' => 'topic-conversations',
            'currentTopics' => Topic::where('team_id', $request->user()->current_team_id)->with('createdBy')->orderBy('created_at', 'DESC')->get(),
            'groupFiles' => function () use ($request) {
                return TopicFile::where([['team_id', $request->user()->current_team_id], ['extension', 'pdf']])
                    ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'xlsx']])
                    ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'doc']])
                    ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'rtf']])
                    ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'txt']])
                    ->get();
            },
            'groupMedia' => function () use ($request) {
                return TopicFile::where([['team_id', $request->user()->current_team_id], ['extension', 'jpg']])
                    ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'png']])
                    ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jpeg']])
                    ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jfif']])
                    ->get();
            },
            'topicName' => $request->topicName ?: "",
            'currentTopic' => $currentTopic,
            'tempUid' => $request->tempUid ?: NULL
        ], 200);
    }

    public function deleteTopicMessage(Request $request): \Illuminate\Http\JsonResponse {
        $message = TopicConversation::where('id', $request->tmid)->first();
        $message->is_deleted = 1;
        $message->save();

        return response()->json('', 200);
    }

    public function deleteTopicMessagePermanently(Request $request): \Illuminate\Http\JsonResponse
    {
        $message = TopicConversation::where('id', $request->tmid)->first();
        if ($message && $message->is_deleted) {
            $message->delete();
        }
        return response()->json([
            'topicConversationId' => $request->tmid
        ], 200);
    }
}
