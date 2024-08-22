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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('designation_id');
            $table->unsignedBigInteger('designation_entry_id');
            $table->string('purchase');
            $table->integer('quantity')->nullable()->default(0);
            $table->timestamps();

            $table->foreign('designation_id')
                ->references('id')
                ->on('designations')
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
        Schema::dropIfExists('purchases');
    }
};
