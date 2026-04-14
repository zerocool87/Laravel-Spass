<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Database\Seeders\AdminUserSeeder::class,
            \Database\Seeders\InstanceSeeder::class,
            \Database\Seeders\ReunionDemoSeeder::class,
            \Database\Seeders\ElusDemoSeeder::class,
            \Database\Seeders\EventDemoSeeder::class,
            \Database\Seeders\DocumentDemoSeeder::class,
            \Database\Seeders\ActualiteDemoSeeder::class,
            \Database\Seeders\ConversationSeeder::class,
            \Database\Seeders\MessageSeeder::class,
        ]);
    }
}
