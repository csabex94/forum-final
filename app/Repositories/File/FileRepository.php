<?php

namespace App\Repositories\File;

use App\Models\File;
use App\Models\Topic;
use App\Models\TopicFile;
use Illuminate\Support\Facades\Storage;

class FileRepository implements FileRepositoryInterface {
    
    protected $file, $topic, $topic_file;
    
    public function __construct(File $file, Topic $topic, TopicFile $topic_file) {
        $this->file = $file;
        $this->topic = $topic;
        $this->topic_file = $topic_file;
    }
    
    public function getByFileName($filename) {
        return $this->file->where('filename', $filename)->first();
    }
    
    public function upload($fileObj) {
        $filename = basename($fileObj->store('public'));
        $originalFilename = $fileObj->getClientOriginalName();
        $fileSize = $fileObj->getSize();
        $ext = $fileObj->getClientOriginalExtension();
    
        $file = new File;
        $file->user_id = request()->user()->id;
        $file->filename = $filename;
        $file->original_filename = $originalFilename;
        $file->size = $fileSize;
        $file->extension = $ext;
        $file->save();

        return $file;
    }
    
    public function delete($id) {
        $file = $this->file->where('id', $id)->first();
        if (!$file) { return; }

        Storage::disk('files')->delete($file->filename);
        $file->delete();
    }

    public function getFavouriteFiles($user_id) {
        return $this->file->where('user_id', $user_id)->where('starred', 1)->get();
    }

    public function getTeamFavouriteFiles($team_id) {
        $topics = $this->topic->where('team_id', $team_id)->get();
        $files = [];

        foreach($topics as $topic) {
            foreach($topic->files as $topic_file) {
                if ($topic_file->starred) {
                    $files[] = $topic_file->file; 
                }
            }
        }

        return $files;
    }
    
}
