<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RoleAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // INSERT ROLES
        // =========================
        $roles = [
            'admin',
            'enseignant',
            's.pedagogique',
            'etudiant'
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['nom' => $role],
                ['nom' => $role, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        // =========================
        // RECUPERER ROLES
        // =========================
        $adminRole = DB::table('roles')->where('nom', 'admin')->first();
        $enseignantRole = DB::table('roles')->where('nom', 'enseignant')->first();
        $secretariatRole = DB::table('roles')->where('nom', 's.pedagogique')->first();
        $etudiantRole = DB::table('roles')->where('nom', 'etudiant')->first();

        // =========================
        // CREER ADMIN (avec les deux mots de passe possibles)
        // =========================
        DB::table('users')->updateOrInsert(
            ['email' => 'marcessone@gmail.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'System',
                'password' => Hash::make('marc1234'), // mot de passe principal
                'role_id' => $adminRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // =========================
        // CREER ENSEIGNANT (version fusionnée)
        // =========================
        DB::table('users')->updateOrInsert(
            ['email' => 'jovaovni@gmail.com'],
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'password' => Hash::make('jova2006'),
                'role_id' => $enseignantRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // =========================
        // CREER SECRETARIAT PEDAGOGIQUE (ajouté)
        // =========================
        DB::table('users')->updateOrInsert(
            ['email' => 'secretariat@example.com'],
            [
                'first_name' => 'Marie',
                'last_name' => 'Curie',
                'password' => Hash::make('secret123'),
                'role_id' => $secretariatRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // =========================
        // CREER ETUDIANT (optionnel - exemple)
        // =========================
        DB::table('users')->updateOrInsert(
            ['email' => 'etudiant@example.com'],
            [
                'first_name' => 'Pierre',
                'last_name' => 'Martin',
                'password' => Hash::make('etudiant123'),
                'role_id' => $etudiantRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}