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
            $table->foreignId('issued_by_user_id')
                ->nullable()
                ->after('issued_by_employee')
                ->constrained('users');

            $table->foreignId('received_by_user_id')
                ->nullable()
                ->after('issued_to_employee')
                ->constrained('users');
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
