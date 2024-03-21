<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            //
        });
        DB::table('departments')
            ->insert([
                ['number' => '06','created_at'=>now(),'updated_at'=>now()],
                ['number' => '07','created_at'=>now(),'updated_at'=>now()],
                ['number' => '08','created_at'=>now(),'updated_at'=>now()],
                ['number' => '14','created_at'=>now(),'updated_at'=>now()],
                ['number' => '25','created_at'=>now(),'updated_at'=>now()],
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            //
        });
    }
};
