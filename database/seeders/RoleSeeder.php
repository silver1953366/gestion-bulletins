<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Optionnel : Désactive les contraintes de clés étrangères pour nettoyer la table proprement
        Schema::disableForeignKeyConstraints();
        Role::truncate();
        Schema::enableForeignKeyConstraints();

        $roles = [
            ['nom' => 'admin'],
            ['nom' => 'enseignant'],
            ['nom' => 'etudiant'],
            ['nom' => 's.pedagogique'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        $this->command->info('Rôles créés avec succès !');
    }
}