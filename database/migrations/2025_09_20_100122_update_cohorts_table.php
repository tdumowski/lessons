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
        Schema::table('cohorts', function (Blueprint $table) {
            $table->unsignedBigInteger('classroom_id')->nullable()->after("teacher_id");

            $table->foreign('classroom_id')->references('id')->on('classrooms');
            $table->unique(['classroom_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cohorts', function (Blueprint $table) {
            //
        });
    }
};
