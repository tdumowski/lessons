<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(
            Classroom::class, // Final model
            "classroom_id" //final model's id in the current model
        );
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(
            Cohort::class, // Final model
            "cohort_id" //final model's id in the current model
        );
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(
            Plan::class, // Final model
            "plan_id" //final model's id in the current model
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

    public function timeslot(): BelongsTo
    {
        return $this->belongsTo(
            Timeslot::class, // Final model
            "timeslot_id" //final model's id in the current model
        );
    }

    public function weekday(): BelongsTo
    {
        return $this->belongsTo(
            Weekday::class, // Final model
            "weekday_id" //final model's id in the current model
        );
    }
}
