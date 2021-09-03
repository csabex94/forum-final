<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicConversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'team_id', 'topic_id', 'message'
    ];

    protected $with = ['createdBy', 'files', 'seenBy'];

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TopicFile::class);
    }
    public function seenBy(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TopicRead::class)->with('user');
    }
    public function team(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Team::class, 'id', 'team_id');
    }

    public function topicReads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TopicRead::class);
    }
}
