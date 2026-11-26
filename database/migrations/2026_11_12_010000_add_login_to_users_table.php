<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('login', 64)->nullable()->unique();
        });
        DB::table('users')->whereNotNull('password')->where('password', '!=', '')
            ->orderBy('id')->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    DB::table('users')->where('id', $user->id)->update(['login' => 'user'.$user->id]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['login']);
            $table->dropColumn('login');
        });
    }
};
