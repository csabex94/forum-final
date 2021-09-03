<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model {

    protected $fillable = ['message', 'team_id'];
    protected $with = ['createdBy', 'seenBy', 'files'];

    public function createdBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function files(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(File::class);
    }

    public function seenBy(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PrivateRead::class, 'private_message_id', 'id')->with('user');
    }
}
