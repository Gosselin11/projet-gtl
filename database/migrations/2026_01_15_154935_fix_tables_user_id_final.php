<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. AJOUTER user_id
        if (!Schema::hasColumn('tables', 'user_id')) {
            Schema::table('tables', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            });

            // Assigner tous les tableaux à user_id = 1
            DB::table('tables')->update(['user_id' => 1]);
        }

        // 2. Clé étrangère
        Schema::table('tables', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. Supprimer project_id si existe
        if (Schema::hasColumn('tables', 'project_id')) {
            Schema::table('tables', function (Blueprint $table) {
                $table->dropColumn('project_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            if (Schema::hasColumn('tables', 'user_id')) {
                $table->dropColumn('user_id');
            }
        });
    }
};
