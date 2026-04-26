<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etudiants', function (Blueprint $table) {
            $table->id();
            // Informations d'identité
            $table->string('nom', 100);
            $table->string('prenom', 100);
            $table->date('date_naissance')->nullable();
            $table->string('lieu_naissance', 100)->nullable();
            
            // Photo de profil
            $table->string('photo')->nullable(); // Ajout de la colonne photo
            
            // Informations académiques (à remplir lors de la finalisation)
            $table->string('bac', 50)->nullable();
            $table->string('provenance', 100)->nullable();
            
            // Système de flux : permet de savoir si l'admin a fini l'inscription
            $table->boolean('is_finalized')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etudiants');
    }
};