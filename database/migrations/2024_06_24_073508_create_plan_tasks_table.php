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
        Schema::create('plan_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_name_id')->nullable();
            $table->integer('category_code')->default(0)->nullable();
            $table->unsignedBigInteger('designation_entry_id');

            $table->string('order_designationEntry');
            $table->string('order_designationEntry_letters');

            $table->integer('quantity');
            $table->integer('quantity_total');
            $table->string('tm')->nullable();
            $table->boolean('is_report_application_statement')->default(0);
            $table->timestamps();

            $table->foreign('order_name_id')
                ->references('id')
                ->on('order_names')
                ->onDelete('cascade');

            $table->foreign('designation_entry_id')
                ->references('id')
                ->on('designations')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_tasks');
    }
};
