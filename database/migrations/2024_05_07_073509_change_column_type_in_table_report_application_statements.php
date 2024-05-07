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
            $table->double('quantity')->change();
            $table->double('quantity_total')->change();
            $table->string('order_number')->change();

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
