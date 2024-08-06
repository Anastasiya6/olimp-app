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
            //$table->dropForeign(['designation_entry_id']);

            // Переименовываем поле
            $table->renameColumn('designation_entry_id', 'designation_id');

            // Добавляем новый внешний ключ и индекс
            $table->foreign('designation_id')
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
        Schema::table('plan_tasks', function (Blueprint $table) {
            $table->dropForeign(['designation_id']);

            // Переименовываем поле обратно
            $table->renameColumn('designation_id', 'designation_entry_id');

            // Добавляем старый внешний ключ и индекс
            $table->foreign('designation_entry_id')
                ->references('id')
                ->on('designations')
                ->onDelete('cascade');
        });
    }
};
