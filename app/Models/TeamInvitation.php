<?php

namespace App\Models;

use Laravel\Jetstream\Jetstream;
use Laravel\Jetstream\TeamInvitation as JetstreamTeamInvitation;
use Spatie\Activitylog\Traits\LogsActivity;

class TeamInvitation extends JetstreamTeamInvitation {

    use LogsActivity;

    protected static $logAttributes = ['email', 'role'];

    protected static $recordEvents = ['created', 'updated', 'deleted'];

    protected static $logName = 'team-invitation';

    protected static $logOnlyDirty = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'role',
        'name',
        'user_id'
    ];

    /**
     * Get the team that the invitation belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Jetstream::teamModel());
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
