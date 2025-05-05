<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('label', 'Administrateur')->first();
        $chefDeChaineRole = Role::where('label', 'Chef de Chaine')->first();
        $qualityRole = Role::where('label', 'Quality')->first();

        $users = [
            [
                'name' => 'Othmane elmeziani',
                'email' => 'othmane.elmeziani@gmail.com',
                'password' => Hash::make('othmane.elmeziani'),
                'role_id' => $adminRole->id ?? null,
            ],
            [
                'name' => 'Afaf',
                'email' => 'afaf@gmail.com',
                'password' => Hash::make('afafafaf'),
                'role_id' => $chefDeChaineRole->id ?? null,
            ],
            [
                'name' => 'Quality User',
                'email' => 'said@gmail.com',
                'password' => Hash::make('saidsaid'),
                'role_id' => $qualityRole->id ?? null,
            ],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(['email' => $user['email']], $user);
        }
    }
}
