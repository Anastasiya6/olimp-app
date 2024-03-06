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
        Schema::create('nacops', function (Blueprint $table) {
            $table->id();
            $table->string('od');
            $table->string('e');
            $table->string('ok');
            $table->integer('pe');
            $table->integer('pi');
            $table->string('na');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nacops');
    }
};
