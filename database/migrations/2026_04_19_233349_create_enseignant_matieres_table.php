<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enseignant_matiere', function (Blueprint $table) {
            $table->id();
            // Liaison vers le profil enseignant
            $table->foreignId('teacher_profile_id')->constrained('teacher_profiles')->onDelete('cascade');
            // Liaison vers la matière
            $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
            $table->timestamps();

            // Empêche les doublons au niveau de la base de données
            $table->unique(['teacher_profile_id', 'matiere_id'], 'unique_teacher_matiere');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enseignant_matiere');
    }
};