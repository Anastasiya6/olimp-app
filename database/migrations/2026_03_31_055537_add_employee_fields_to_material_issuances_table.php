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
            $table->string('issued_to_employee')->after('id'); // хто отримує
            $table->string('issued_by_employee')->after('issued_to_employee'); // хто виписує
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_issuances', function (Blueprint $table) {
            $table->dropColumn(['issued_to_employee', 'issued_by_employee']);
        });
    }
};
