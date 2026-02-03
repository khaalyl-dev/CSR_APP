<?php

namespace Database\Seeders;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Create test users and sites for development.
     *
     * Test accounts (mot de passe : password) :
     * - corporate@test.com  → Vue globale (corporate)
     * - plant1@test.com     → Interface site (Lyon)
     * - plant2@test.com     → Interface site (Paris)
     */
    public function run(): void
    {
        $siteLyon = Site::firstOrCreate(
            ['site_name' => 'Site Lyon'],
            ['location' => 'Lyon', 'manager' => 'Jean Dupont']
        );
        $siteParis = Site::firstOrCreate(
            ['site_name' => 'Site Paris'],
            ['location' => 'Paris', 'manager' => 'Marie Martin']
        );

        $password = Hash::make('password');

        // Corporate user (accès vue globale)
        User::firstOrCreate(
            ['email' => 'corporate@test.com'],
            [
                'username' => 'corporate',
                'password' => $password,
                'role' => 'corporate',
                'site_id' => null,
            ]
        );

        // Plant user – Site Lyon
        User::firstOrCreate(
            ['email' => 'plant1@test.com'],
            [
                'username' => 'plant_lyon',
                'password' => $password,
                'role' => 'plant',
                'site_id' => $siteLyon->site_id,
            ]
        );

        // Plant user – Site Paris
        User::firstOrCreate(
            ['email' => 'plant2@test.com'],
            [
                'username' => 'plant_paris',
                'password' => $password,
                'role' => 'plant',
                'site_id' => $siteParis->site_id,
            ]
        );
    }
}
