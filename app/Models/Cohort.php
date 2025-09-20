<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Cohort extends Model
{
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(
            Classroom::class, // Final model
            "classroom_id" //final model's id in the current model
        );
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(
            School::class, // Final model
            "school_id" //final model's id in the current model
        );
    }

    public function subjects(): HasManyThrough
    {
        return $this->hasManyThrough(
            Subject::class, // Final model
            CohortSubject::class, // Intermediate model
            "cohort_id", //current model's id in Intermediate model
            "id",
            "id",
            "subject_id" //final model's id in Intermediate model
        );
    }

    public function mainTeacher(): BelongsTo
    {
        return $this->belongsTo(
            Teacher::class, // Final model
            "teacher_id" //final model's id in the current model
        );
    }

    public function teachers(): HasManyThrough
    {
        return $this->hasManyThrough(
            Teacher::class, // Final model
            CohortSubject::class, // Intermediate model
            "cohort_id", //current model's id in Intermediate model
            "id",
            "id",
            "teacher_id" //final model's id in Intermediate model
        );
    }
}
