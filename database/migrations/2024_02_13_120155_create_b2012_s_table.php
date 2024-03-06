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
        Schema::create('b2012_s', function (Blueprint $table) {
            $table->id();
            $table->string('kuda');
            $table->string('zakaz');
            $table->string('chto');
            $table->string('kols');
            $table->string('kolzak');
            $table->string('tm')->nullable();
            $table->string('tm1')->nullable();
            $table->string('naim')->nullable();
            $table->string('hcp')->nullable();
            $table->string('e')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b2012_s');
    }
};
