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
            $table->unsignedBigInteger('season_id')->nullable()->after("id");

            $table->foreign('season_id')->references('id')->on('seasons');
            $table->unique(['season_id', 'school_id', 'level', 'line']);
        });

        Schema::table('schools', function (Blueprint $table) {
            $table->tinyInteger('seasons')->nullable()->after("address")->comment('ile lat trwa nauka w danej szkole');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
