<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('import_material_id')->constrained('import_materials');
            $table->decimal('quantity', 12, 3);
            $table->foreignId('department_id')->constrained('departments');
            $table->enum('status', ['draft', 'posted'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_returns');
    }
};
