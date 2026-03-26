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
        Schema::create('import_materials', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable();
            $table->string('article')->nullable();
            $table->unique(['code', 'article']);
            $table->string('name');
            $table->unsignedBigInteger('type_unit_id')->nullable();
            $table->foreign('type_unit_id')->references('id')->on('type_units')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_materials');
    }
};
