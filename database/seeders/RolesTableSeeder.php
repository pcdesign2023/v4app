<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['label' => 'Administrateur'],
            ['label' => 'Chef de Chaine'],
            ['label' => 'Quality'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate($role);
        }
    }
}
