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
        Schema::table('material_issuance_items', function (Blueprint $table) {
            $table->unsignedBigInteger('designation_id')->after('material_id')->nullable();

            $table->foreign('designation_id')->references('id')->on('designations');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_issuance_items', function (Blueprint $table) {
            //
        });
    }
};
