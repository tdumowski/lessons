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
        Schema::create('weekdays', function (Blueprint $table) {
            $table->id();
            $table->enum('name', ['Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek']);
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });

        Schema::table('lessons', function($table) {
            $table->renameColumn('weekday', 'weekday_id');
        });

        Schema::table('lessons', function($table) {
            $table->foreign('weekday_id')->references('id')->on('weekdays');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function($table) {
            $table->dropForeign('weekday_id');
        });

        Schema::dropIfExists('weekdays');
    }
};
