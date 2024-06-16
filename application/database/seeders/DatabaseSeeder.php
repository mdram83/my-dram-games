<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
         User::factory()->create([
             'name' => 'test1',
             'email' => 'test1@example.com',
             'password' => Hash::make('pwd'),
         ]);

        User::factory()->create([
            'name' => 'test2',
            'email' => 'test2@example.com',
            'password' => Hash::make('pwd'),
        ]);

        User::factory()->create([
            'name' => 'test3',
            'email' => 'test3@example.com',
            'password' => Hash::make('pwd'),
        ]);

        User::factory()->create([
            'name' => 'test4',
            'email' => 'test4@example.com',
            'password' => Hash::make('pwd'),
        ]);
    }
}
