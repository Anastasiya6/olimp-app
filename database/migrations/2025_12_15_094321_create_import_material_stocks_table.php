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
        Schema::create('import_material_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_material_id')
                ->constrained('import_materials')
                ->cascadeOnDelete();
            $table->decimal('amount', 12, 3);
            $table->string('document_number')->nullable();
            $table->dateTime('document_date')->comment('Data of document')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->enum('type', ['stock', 'stock_in', 'stock_out'])
                ->comment('stock = початковий залишок, stock_in = прихід', 'stock_out = витрата');
            $table->foreign('department_id')
                ->references('id')
                ->on('departments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_material_stocks');
    }
};
