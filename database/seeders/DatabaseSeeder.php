<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'name' => 'Demo Manager',
            'email' => 'manager@leaddesk.test',
            'password' => 'password',
        ]);

        $this->call(LeadSeeder::class);
    }
}
