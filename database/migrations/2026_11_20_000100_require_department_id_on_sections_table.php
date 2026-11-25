<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('sections')->whereNull('department_id')->exists()) {
            throw new \RuntimeException('Призначте цех усім ділянкам перед застосуванням міграції.');
        }

        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable(false)->change();
            $table->foreign('department_id')->references('id')->on('departments')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
        });
        Schema::table('sections', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->change();
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
        });
    }
};
