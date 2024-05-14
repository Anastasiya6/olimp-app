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
        Schema::table('report_application_statements', function (Blueprint $table) {
            $table->index(['designation_entry_id', 'designation_id', 'order_number', 'category_code'], 'report_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_application_statements', function (Blueprint $table) {
            //
        });
    }
};
