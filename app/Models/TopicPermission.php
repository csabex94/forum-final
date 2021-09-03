<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'topic_id', 'team_id', 'permission'
    ];
}
