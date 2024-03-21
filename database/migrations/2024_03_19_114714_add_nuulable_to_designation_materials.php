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
        Schema::table('designation_materials', function (Blueprint $table) {
            $table->string('designation_from_excel')->nullable()->change();
            $table->string('material_from_excel')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('designation_materials', function (Blueprint $table) {
            //
        });
    }
};
