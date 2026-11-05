<?php

namespace Database\Seeders;

use App\Models\Lead;
use Illuminate\Database\Seeder;

class LeadSeeder extends Seeder
{
    public function run(): void
    {
        Lead::factory()->count(12)->create();

        Lead::factory()->create([
            'name' => 'Demo Lead',
            'email' => 'demo.lead@example.com',
            'phone' => '+380500000000',
            'company' => 'Acme LLC',
            'message' => 'Interested in a product demo.',
            'status' => 'new',
            'notes' => null,
        ]);
    }
}
