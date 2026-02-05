<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $existingAdmin = User::where('email', 'admin@example.com')->first();
        
        if ($existingAdmin) {
            $this->command->info('Admin user already exists!');
            $this->command->info('Email: admin@example.com');
            $this->command->info('Password: password');
            $this->command->info('Is admin: ' . ($existingAdmin->is_admin ? 'Yes' : 'No'));
            $this->command->info('Is elu: ' . ($existingAdmin->is_elu ? 'Yes' : 'No'));
            return;
        }

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'is_elu' => true,
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: password');
    }
}