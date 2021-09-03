<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Models\TopicOpened;


class Topic extends Model {


    public function createdBy(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Team::class);
    }


    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TopicFile::class, 'topic_id', 'id');
    }

    public function topicConversations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TopicConversation::class)->whereHas('seenBy', function($q) {
            $q->where([
                ['wseen', NULL],
                ['user_id', auth()->user()->id]
            ]);
        });
    }


    public function userPermissions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TopicPermission::class, 'topic_id', 'id');
    }

    public function topicOpeneds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TopicOpened::class);
    }
}

