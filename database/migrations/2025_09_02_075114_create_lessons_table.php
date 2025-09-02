<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->unsignedBigInteger('weekday');
            $table->unsignedBigInteger('timeslot_id');
            $table->unsignedBigInteger('classroom_id');
            $table->unsignedBigInteger('cohort_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('teacher_id');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('plan_id')->references('id')->on('plans');
            $table->foreign('timeslot_id')->references('id')->on('timeslots');
            $table->foreign('classroom_id')->references('id')->on('classrooms');
            $table->foreign('cohort_id')->references('id')->on('cohorts');
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('teacher_id')->references('id')->on('teachers');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
