<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultats_ues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etudiant_id')->nullable()->constrained('etudiants')->onDelete('cascade');
            $table->foreignId('ue_id')->nullable()->constrained('ues')->onDelete('cascade');
            $table->decimal('moyenne', 5, 2)->nullable();
            $table->integer('credits_acquis')->default(0);
            $table->boolean('compense')->default(false);
            $table->unique(['etudiant_id', 'ue_id'], 'unique_ru');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultats_ues');
    }
};