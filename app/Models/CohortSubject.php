<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CohortSubject extends Model
{
    public function cohort(): BelongsTo
    {
        return $this->belongsTo(
            Cohort::class, // Final model
            "cohort_id" //final model's id in the current model
        );
    }
    
    public function subject(): BelongsTo
    {
        return $this->belongsTo(
            Subject::class, // Final model
            "subject_id" //final model's id in the current model
        );
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(
            Teacher::class, // Final model
            "teacher_id" //final model's id in the current model
        );
    }
}
