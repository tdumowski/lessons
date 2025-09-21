<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSchool extends Model
{
    public function school(): BelongsTo
    {
        return $this->belongsTo(
            School::class, // Final model
            "school_id" //final model's id in the current model
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class, // Final model
            "user_id" //final model's id in the current model
        );
    }
}
