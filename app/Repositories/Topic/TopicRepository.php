<?php

namespace App\Repositories\Topic;

use App\Models\Contact;
use App\Models\Team;
use App\Models\Topic;
use App\Models\TopicConversation;
use App\Models\TopicFile;
use App\Repositories\File\FileRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;


class TopicRepository implements TopicRepositoryInterface {

    protected $topic, $team, $topic_file, $fileRepository;

    public function __construct(Topic $topic, Team $team, TopicFile $topic_file) {
        $this->topic = $topic;
        $this->team = $team;
        $this->topic_file = $topic_file;
        $this->fileRepository = app()->make(FileRepositoryInterface::class);
    }

    public function getTopic($id) {
        return $this->topic->find($id);
    }

    public function getTopics($team_id) {
        return $this->topic->where('team_id', $team_id)->get();
    }

    public function createTopic($request) {
        $request->validate([
            'title' => 'required|string',
            'description' => 'string',
            'team_id' => 'required|integer'
        ]);

        if(count($this->topic->where('title', $request->title)->where('team_id', $request->team_id)->get())) {
            return response()->json(['error' => 'Topic already exists.']);
        }

        $topic = new Topic();
        $topic->title = $request->title;
        $topic->team_id = $request->team_id;
        $topic->user_id = $request->user()->id;
        if (isset($request->description)) {
            $topic->description = $request->description;
        }
        $topic->save();

        return Inertia::render('Home', [
            'currentTopics' => Topic::where('team_id', $request->team_id)->with('createdBy')->orderBy('created_at', 'DESC')->get(),
            'rightSide' => 'groups',
            'middleSection' => 'topics',
            'contacts' => Contact::where('user_id', $request->user()->id)->orWhere('friend_id', $request->user()->id)->with(['friend', 'user'])->get()
        ]);
    }

    public function storeTopicConversation($request) {
        $request->validate([
            'message' => 'required|string',
            'topicId' => 'required|integer'
        ]);

        $message = new TopicConversation();
        $message->message = $request->message;
        $message->topic_id = $request->topicId;
        $message->user_id = $request->user()->id;
        $message->team_id = $request->user()->current_team_id;
        $message->save();

        return Inertia::render('Home', [
            'currentTopicConversations' => TopicConversation::where([
                ['team_id', $request->user()->current_team_id],
                ['topic_id', $request->topicId]
            ])->with('file')->with('createdBy')->orderBy('created_at', 'ASC')->get(),
            'middleSection' => $request->topicId ? 'topic-conversations' : 'topics',
        ]);
    }

    public function storeTopicFileConversation($request) {
        $file = $this->fileRepository->upload($request->file('file'));
        if(!$file) {
            return response()->json(['error' => 'File could not be uploaded'], 500);
        }
        $message = new TopicConversation();
        $message->user_id = $request->user()->id;
        $message->team_id = $request->user()->current_team_id;
        $message->topic_id = $request->topicId;
        $message->file_id = $file->id;
        $message->message = $request->message;
        $message->save();

        return Inertia::render('Home', [
            'currentTopicConversations' => TopicConversation::where([
                ['team_id', $request->user()->current_team_id],
                ['topic_id', $request->topicId]
            ])->with('file')->with('createdBy')->orderBy('created_at', 'ASC')->get(),
            'currentTopics' => Topic::where('team_id', $request->team_id)->with('createdBy')->orderBy('created_at', 'DESC')->get(),
            'rightSide' => 'groups',
            'middleSection' => 'topics',
        ]);
    }

    public function updateTopic($request) {
        $request->validate([
            'title' => 'required|string',
            'description' => 'string',
            'topic_id' => 'required|integer'
        ]);

        $topic = $this->topic->find($request->topic_id);

        $topic->title = $request->title;
        $topic->description = ($request->description) ?? '';

        $topic->save();

        return Inertia::render('Home', [
            'currentTopicConversations' => TopicConversation::where([
                ['team_id', $request->user()->current_team_id],
                ['topic_id', $request->topic_id]
            ])->with('file')->with('createdBy')->orderBy('created_at', 'ASC')->get(),
            'middleSection' => $request->topic_id ? 'topic-conversations' : 'topics',
        ]);
    }

    public function updateGroupPhoto($request) {
        $request->validate([
            'photo' => 'required|mimes:jpg,jpeg,png|max:2048'
        ]);

        $team = $this->team->find($request->user()->current_team_id);
        $this->updateGroupProfilePhoto($team, $request->file('photo'));

        return redirect()->back();
    }

    private function updateGroupProfilePhoto(Team $team, UploadedFile $photo) {
        $previous = ($team->profile_photo_path) ?? false;

        $team->profile_photo_path = $photo->storePublicly('profile-photos', ['disk' => 'public']);
        $team->save();

        if ($previous) {
            Storage::disk('public')->delete('profile-photos/' . $previous);
        }

    }



    public function deleteTopic($topic_id, $delete_files = false): bool
    {
        $topic = $this->topic->where('id', $topic_id)->first();
        if(!$topic) {
            return false;
        }
        if (count($topic->messages)) {
            foreach($topic->messages as $message) {
                $message->delete();
            }
        }

        if (count($topic->files) && $delete_files) {
            foreach($topic->files as $topic_file) {
                Storage::disk('public')->delete($topic_file->file->filename);
                $topic_file->delete();
            }
        }

        $topic->delete();

        return true;
    }

    public function createTopicFile($file_id, $topic_id) {
        $topicFile = new TopicFile();
        $topicFile->file_id = $file_id;
        $topicFile->topic_id = $topic_id;

        $topicFile->save();

    }

    public function deleteTopicFile($topic_file_id) { // tested, works
        $topic_file = $this->topic_file->find($topic_file_id);
        if (!$topic_file) {
            return false;
        }

        Storage::disk('public')->delete($topic_file->file->filename);
        $topic_file->file->delete();
        $topic_file->delete();

        return true;
    }

}
