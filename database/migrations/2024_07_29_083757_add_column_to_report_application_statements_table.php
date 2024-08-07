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
            $table->unsignedBigInteger('order_name_id')->nullable()->after('order_number');

            $table->unsignedBigInteger('order_id')->nullable()->after('order_name_id');

            $table->foreign('order_name_id')
                ->references('id')
                ->on('order_names')
                ->onDelete('cascade');

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');
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
