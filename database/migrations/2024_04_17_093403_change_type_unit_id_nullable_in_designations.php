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
        // Изменить тип колонки на nullable
        Schema::table('designations', function (Blueprint $table) {
            $table->bigInteger('type_unit_id')->nullable()->change();
        });
     }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('designations', function (Blueprint $table) {
            //
        });
    }
};
