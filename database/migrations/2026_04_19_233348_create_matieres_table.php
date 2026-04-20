<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matieres', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->nullable();
            $table->string('libelle', 255)->nullable();
            $table->integer('coefficient')->nullable();
            $table->integer('credits')->nullable();
            $table->foreignId('ue_id')->nullable()->constrained('ues')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matieres');
    }
};