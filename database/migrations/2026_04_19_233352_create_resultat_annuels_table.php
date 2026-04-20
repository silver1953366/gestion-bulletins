<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultats_annuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->nullable()->constrained('etudiants')->onDelete('cascade');
            $table->foreignId('annee_academique_id')->nullable()->constrained('annees_academiques')->onDelete('cascade');
            $table->decimal('moyenne', 5, 2)->nullable();
            $table->string('decision', 100)->nullable();
            $table->string('mention', 50)->nullable();
            $table->unique(['etudiant_id', 'annee_academique_id'], 'unique_ra');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultats_annuels');
    }
};