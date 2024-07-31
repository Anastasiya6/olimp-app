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
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->unsignedBigInteger('sender_department_id')->nullable();
            $table->unsignedBigInteger('receiver_department_id')->nullable();

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
        Schema::table('plan_tasks', function (Blueprint $table) {
            //
        });
    }
};
