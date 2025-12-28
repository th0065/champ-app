<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. CRÉATION DE L'ADMIN (Gestion complète)
        User::firstOrCreate(
            ['email' => 'admin@arame.com'],
            [
                'name' => 'Admin Arame',
                'password' => Hash::make('admin123'), // Mot de passe sécurisé
                'role' => 'admin',
                'phone' => '770000001',
                'email_verified_at' => now(),
            ]
        );

        // 2. CRÉATION D'UN LIVREUR (Vue livraisons et carte)
        User::firstOrCreate(
            ['email' => 'livreur@arame.com'],
            [
                'name' => 'Modou Livreur',
                'password' => Hash::make('livreur123'),
                'role' => 'driver',
                'phone' => '770000002',
                'email_verified_at' => now(),
            ]
        );

        // 3. CRÉATION D'UN CLIENT / ACHETEUR (Catalogue et commande)
        User::firstOrCreate(
            ['email' => 'client@gmail.com'],
            [
                'name' => 'Awa Cliente',
                'password' => Hash::make('client123'),
                'role' => 'buyer',
                'phone' => '770000003',
                'email_verified_at' => now(),
            ]
        );
    }
}