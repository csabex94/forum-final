<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class File extends Model {
    use LogsActivity;

    protected static $logAttributes = ['filename', 'original_filename', 'extension'];

    protected static $recordEvents = ['created', 'deleted'];

    protected static $logName = 'file';

    protected static $logOnlyDirty = true;

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
