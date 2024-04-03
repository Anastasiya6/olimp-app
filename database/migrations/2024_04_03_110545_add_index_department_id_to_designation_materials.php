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
        Schema::table('designation_materials', function (Blueprint $table) {
            $table->bigInteger('department_id')->unsigned()->nullable()->after('norm');
            $table->foreign('department_id')->references('id')->on('departments');

        });
        DB::table('designation_materials')->update(['department_id' => 5]);
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
