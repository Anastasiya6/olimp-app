<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('material_coefficients')->insert([
            [
                'keyword' => 'Лист',
                'coefficient' => 1.2,
            ],
            [
                'keyword' => 'Плита',
                'coefficient' => 1.2,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
