<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Classroom extends Model
{
    
    public function subjects(): HasManyThrough
    {
        return $this->hasManyThrough(
            Subject::class, // Final model
            ClassroomSubject::class, // Intermediate model
            "classroom_id", //current model's id in Intermediate model
            "id",
            "id",
            "subject_id" //final model's id in Intermediate model
        );
    }
}
