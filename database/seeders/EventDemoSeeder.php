<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        Event::factory()->count(10)->create([
            'created_by' => $user ? $user->id : null,
        ]);
    }
}
