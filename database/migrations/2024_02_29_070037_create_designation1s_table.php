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
        Schema::create('designation1s', function (Blueprint $table) {
            $table->id();
            $table->string('designation')->unique();
            $table->string('name')->nullable();
            $table->string('route')->nullable();
            $table->string('gost')->nullable();
            $table->string('type_units')->nullable();
            $table->tinyInteger('type')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designation1s');
    }
};
