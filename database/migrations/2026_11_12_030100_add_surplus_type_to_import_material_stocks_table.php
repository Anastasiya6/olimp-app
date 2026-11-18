<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('import_material_stocks', function (Blueprint $table) {
            $table->enum('type', ['stock', 'stock_in', 'stock_out', 'return', 'surplus'])
                ->comment('stock = opening balance, stock_in = receipt, stock_out = expense, return = material return, surplus = material surplus')
                ->change();
        });
    }

    public function down(): void
    {
        if (DB::table('import_material_stocks')->where('type', 'surplus')->exists()) {
            throw new \RuntimeException('Cannot remove surplus type while surplus stock movements exist.');
        }
        Schema::table('import_material_stocks', function (Blueprint $table) {
            $table->enum('type', ['stock', 'stock_in', 'stock_out', 'return'])
                ->comment('stock = opening balance, stock_in = receipt, stock_out = expense, return = material return')
                ->change();
        });
    }
};
