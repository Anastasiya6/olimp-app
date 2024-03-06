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
        Schema::create('report_application_statements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('designation_id');
            $table->integer('category_code')->default(0)->nullable();
            $table->unsignedBigInteger('designation_entry_id');
            $table->integer('quantity');
            $table->integer('quantity_total');
            $table->integer('order_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_application_statements');
    }
};
