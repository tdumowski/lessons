<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Log extends Model
{
    public function logItem(): BelongsTo
    {
        return $this->belongsTo(
            LogItem::class, // Final model
            "logitem_id" //final model's id in the current model
        );
    }
}
