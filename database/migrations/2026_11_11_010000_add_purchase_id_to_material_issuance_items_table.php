<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_issuance_items', function (Blueprint $table) {
            $table->foreignId('purchase_id')->nullable()->constrained('purchases');
        });
    }

    public function down(): void
    {
        Schema::table('material_issuance_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('purchase_id');
        });
    }
};
