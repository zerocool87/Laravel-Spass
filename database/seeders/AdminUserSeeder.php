<?php

namespace Database\Seeders;

use App\Models\EluProfile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'prenom' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_elu' => true,
        ]);

        EluProfile::create([
            'user_id' => $admin->id,
            'civilite' => 'Monsieur',
        ]);

        $this->command->info('Admin user created!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: password');
    }
}
