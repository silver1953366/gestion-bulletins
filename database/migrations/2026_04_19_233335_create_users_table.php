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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email', 255)->unique();
            $table->string('password', 255);
            
            /** * Champ photo optionnel.
             * L'ordre dans cette liste définit l'ordre dans la base de données.
             */
            $table->string('photo')->nullable();

            /**
             * Relation avec la table roles.
             * nullOnDelete() permet de garder l'utilisateur même si son rôle est supprimé.
             */
            $table->foreignId('role_id')
                  ->nullable()
                  ->constrained('roles')
                  ->nullOnDelete();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};