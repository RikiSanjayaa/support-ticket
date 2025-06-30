<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Support Agent',
            'email' => 'agent@gmail.com',
            'password' => 'password',
            'role' => 'agent',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        // Create additional users
        User::factory(3)->create(['role' => 'user']);
        User::factory(2)->create(['role' => 'agent']);

        // Create tickets
        Ticket::factory(20)->create();
    }
}
