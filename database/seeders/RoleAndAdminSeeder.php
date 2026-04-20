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
            'secretaire',
            'etudiant'
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['nom' => $role],
                ['nom' => $role]
            );
        }

        // =========================
        // RECUPERER ROLE ADMIN
        // =========================
        $adminRole = DB::table('roles')->where('nom', 'admin')->first();

        // =========================
        // CREER ADMIN
        // =========================
        DB::table('users')->updateOrInsert(
            ['email' => 'moulekageorges@gmail.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'System',
                'password' => Hash::make('jova2006'), // change après
                'role_id' => $adminRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}