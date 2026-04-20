<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultats_semestres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->nullable()->constrained('etudiants')->onDelete('cascade');
            $table->foreignId('semestre_id')->nullable()->constrained('semestres')->onDelete('cascade');
            $table->foreignId('annee_academique_id')->nullable()->constrained('annees_academiques')->onDelete('cascade');
            $table->decimal('moyenne', 5, 2)->nullable();
            $table->integer('credits_total')->nullable();
            $table->boolean('valide')->default(false);
            $table->unique(['etudiant_id', 'semestre_id', 'annee_academique_id'], 'unique_rs');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultats_semestres');
    }
};