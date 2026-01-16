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
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
    $table->foreignId('project_id')->constrained()->onDelete('cascade');
    $table->string('name')->default('Nouveau Tableau'); // Remplace title par name
    $table->integer('rows_count')->default(3); // N'oublie pas cette ligne pour le bouton + Ligne
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
