<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicOpened extends Model
{
    use HasFactory;

    protected $fillable = [
        'topic_id', 'user_id', 'team_id', 'status'
    ];
}
