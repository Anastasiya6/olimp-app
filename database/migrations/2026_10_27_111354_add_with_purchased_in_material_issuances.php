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
        Schema::table('material_issuances', function (Blueprint $table) {
            $table->boolean('with_material_purchased')->default(0)->after('quantity');
            $table->boolean('with_purchased')->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_issuances', function (Blueprint $table) {
            //
        });
    }
};
