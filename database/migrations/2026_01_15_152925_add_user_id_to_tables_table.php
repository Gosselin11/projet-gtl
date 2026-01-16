<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. AJOUTER la colonne user_id
        Schema::table('tables', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id');
        });

        // 2. ASSIGNER user_id = 1 à TOUS les tableaux existants
        DB::table('tables')->update(['user_id' => 1]);

        // 3. RENDRE NON-NULLABLE
        Schema::table('tables', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->dropColumn('project_id'); // Supprime l'ancienne colonne si elle existe
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
