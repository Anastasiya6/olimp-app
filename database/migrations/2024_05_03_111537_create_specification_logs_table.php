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
        Schema::create('specification_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('designation_id')->nullable();
            $table->unsignedBigInteger('designation_entry_id')->nullable();
            $table->unsignedBigInteger('material_id')->nullable();

            $table->string('designation_number')->nullable();
            $table->string('detail_number')->nullable();
            $table->string('designation')->nullable();
            $table->string('detail')->nullable();
            $table->string('material')->nullable();
            $table->string('message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specification_logs');
    }
};
