<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassroomSubject extends Model
{
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(
            Classroom::class, // Final model
            "classroom_id" //final model's id in the current model
        );
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(
            Subject::class, // Final model
            "subject_id" //final model's id in the current model
        );
    }
}
