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
        Schema::table('classroom_subjects', function (Blueprint $table) {
            $table->tinyInteger('exclusive')->after("subject_id")->default(0)->comment('is classroom assigned to the subject exclusively');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('classroom_subjects', function (Blueprint $table) {
            //
        });
    }
};
