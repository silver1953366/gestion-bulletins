<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultats_matieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->nullable()->constrained('etudiants')->onDelete('cascade');
            $table->foreignId('matiere_id')->nullable()->constrained('matieres')->onDelete('cascade');
            $table->decimal('moyenne', 5, 2)->nullable();
            $table->boolean('utilise_rattrapage')->default(false);
            $table->unique(['etudiant_id', 'matiere_id'], 'unique_rm');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultats_matieres');
    }
};