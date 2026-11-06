<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_create_a_lead(): void
    {
        $response = $this->postJson('/api/leads', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '+380501112233',
            'company' => 'Acme',
            'message' => 'Need a demo',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Jane Doe')
            ->assertJsonPath('data.email', 'jane@example.com')
            ->assertJsonPath('data.status', 'new');

        $this->assertDatabaseHas('leads', [
            'email' => 'jane@example.com',
            'status' => 'new',
        ]);
    }

    public function test_create_lead_validation_requires_name_and_email(): void
    {
        $response = $this->postJson('/api/leads', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email']);
    }

    public function test_guest_cannot_list_leads(): void
    {
        $this->getJson('/api/leads')->assertUnauthorized();
    }

    public function test_authenticated_user_can_list_leads(): void
    {
        $user = User::factory()->create();
        Lead::factory()->count(2)->create();

        $response = $this->actingAs($user)->getJson('/api/leads');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_authenticated_user_can_filter_leads_by_status(): void
    {
        $user = User::factory()->create();
        Lead::factory()->create(['status' => 'new', 'email' => 'new@example.com']);
        Lead::factory()->create(['status' => 'won', 'email' => 'won@example.com']);

        $response = $this->actingAs($user)->getJson('/api/leads?status=won');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.email', 'won@example.com');
    }

    public function test_authenticated_user_can_filter_leads_for_today(): void
    {
        $user = User::factory()->create();

        Lead::factory()->create([
            'email' => 'today@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Lead::factory()->create([
            'email' => 'old@example.com',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($user)->getJson('/api/leads?today=1');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.email', 'today@example.com');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'manager@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk()
            ->assertJsonPath('user.email', 'manager@example.com');

        $this->assertAuthenticated();
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'manager@example.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'manager@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable();
        $this->assertGuest();
    }
}
