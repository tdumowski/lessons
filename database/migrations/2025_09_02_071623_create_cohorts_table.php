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
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->tinyInteger('level')->default(1)->comment('poziom rocznikowy: klasa 1, 2 itd');
            $table->string('line', 1)->nullable()->comment('litera w danym roczniku: a, b itd.');
            $table->string('profile', 25)->nullable()->comment('profil klasy: językowa, dziennikarska itd.');
            $table->unsignedBigInteger('teacher_id')->nullable()->comment('wychowawca klasy');
            $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools');
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
        Schema::dropIfExists('cohorts');
    }
};
