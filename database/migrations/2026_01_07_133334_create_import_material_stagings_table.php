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
        Schema::create('import_material_stagings', function (Blueprint $table) {
            $table->id();
            $table->string('article');
            $table->string('name')->nullable();
            $table->string('document_number')->nullable();
            $table->integer('quantity')->nullable();
            $table->date('document_date')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('type_unit_id')->nullable();
            $table->enum('status', ['new', 'conflict', 'processed'])->default('new');
            $table->unsignedBigInteger('resolved_material_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_material_stagings');
    }
};
