<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'role' => 'admin',
            ]
        );
        
        // Optional: Create standard user for testing
        User::firstOrCreate(
            ['username' => 'user'],
            [
                 'name' => 'Test User',
                 'email' => 'test@example.com',
                 'password' => \Illuminate\Support\Facades\Hash::make('password'),
                 'role' => 'user',
            ]
        );
        $this->call([
            TableColumnSeeder::class,
        ]);
    }
}
