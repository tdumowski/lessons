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
        Schema::table('plans', function (Blueprint $table) {
            $table->tinyInteger('test_soft')->nullable()->after("test_details")->comment('null:test not performed|1:passed|0:failed');
            $table->text('test_soft_details')->nullable()->after("test_soft")->comment('failed test details');
            $table->renameColumn('test', 'test_critical');
            $table->renameColumn('test_details', 'test_critical_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            //
        });
    }
};
