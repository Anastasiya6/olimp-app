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
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->string('document_number');
            $table->unsignedBigInteger('designation_id');
            $table->integer('quantity');
            $table->unsignedBigInteger('sender_department_id')->nullable();
            $table->unsignedBigInteger('receiver_department_id')->nullable();

            $table->unsignedBigInteger('material_id')->nullable();
            $table->dateTime('document_date')->comment('Data of document')->nullable();

            $table->boolean('is_written_off')->nullable()->default(0);
            $table->dateTime('start_date_write_off')->comment('Start Date for Write Off')->nullable();
            $table->dateTime('end_date_write_off')->comment('End Date for Write Off')->nullable();

            $table->timestamps();

            $table->foreign('designation_id')
                ->references('id')
                ->on('designations')
                ->onDelete('cascade');

            $table->foreign('sender_department_id')
                ->references('id')
                ->on('departments');
            $table->foreign('receiver_department_id')
                ->references('id')
                ->on('departments');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
    }
};
