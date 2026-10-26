<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $manager;
    private User $cashier;
    private Customer $customerAlice;
    private Customer $customerBob;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'name' => 'Store Owner',
            'email' => 'owner@delivergate.com',
            'password' => Hash::make('Password123!'),
            'role' => UserRole::OWNER,
        ]);

        $this->manager = User::create([
            'name' => 'Store Manager',
            'email' => 'manager@delivergate.com',
            'password' => Hash::make('Password123!'),
            'role' => UserRole::MANAGER,
        ]);

        $this->cashier = User::create([
            'name' => 'Store Cashier',
            'email' => 'cashier@delivergate.com',
            'password' => Hash::make('Password123!'),
            'role' => UserRole::CASHIER,
        ]);

        $this->customerAlice = Customer::create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'phone' => '+1-555-0101',
            'address' => '742 Evergreen Terrace',
            'notes' => 'Prefers gluten-free cones.',
        ]);

        $this->customerBob = Customer::create([
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'phone' => '+1-555-0102',
            'address' => '12 Grimmauld Place',
            'notes' => 'Regular customer.',
        ]);
    }

    public function test_all_roles_can_view_customers(): void
    {
        foreach ([$this->owner, $this->manager, $this->cashier] as $user) {
            $response = $this->actingAs($user)->getJson('/api/customers');
            $response->assertOk()
                ->assertJsonCount(2, 'data');
        }
    }

    public function test_all_roles_can_view_single_customer(): void
    {
        foreach ([$this->owner, $this->manager, $this->cashier] as $user) {
            $response = $this->actingAs($user)->getJson("/api/customers/{$this->customerAlice->id}");
            $response->assertOk()
                ->assertJsonPath('data.name', 'Alice Johnson');
        }
    }

    public function test_searching_customers_by_name_or_phone(): void
    {
        $response = $this->actingAs($this->cashier)->getJson('/api/customers?search=Alice');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Alice Johnson');

        $phoneResponse = $this->actingAs($this->cashier)->getJson('/api/customers?search=0102');
        $phoneResponse->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Bob Smith');
    }

    public function test_all_roles_can_create_customer(): void
    {
        // 1. Owner creates customer
        $respOwner = $this->actingAs($this->owner)->postJson('/api/customers', [
            'name' => 'Charlie Day',
            'email' => 'charlie@example.com',
            'phone' => '+1-555-0201',
        ]);
        $respOwner->assertCreated();

        // 2. Manager creates customer
        $respManager = $this->actingAs($this->manager)->postJson('/api/customers', [
            'name' => 'Dennis Reynolds',
            'email' => 'dennis@example.com',
            'phone' => '+1-555-0202',
        ]);
        $respManager->assertCreated();

        // 3. Cashier creates customer
        $respCashier = $this->actingAs($this->cashier)->postJson('/api/customers', [
            'name' => 'Mac McDonald',
            'email' => 'mac@example.com',
            'phone' => '+1-555-0203',
        ]);
        $respCashier->assertCreated();

        $this->assertDatabaseCount('customers', 5);
    }

    public function test_all_roles_can_update_customer(): void
    {
        $response = $this->actingAs($this->cashier)->putJson("/api/customers/{$this->customerAlice->id}", [
            'phone' => '+1-555-9999',
            'notes' => 'Updated notes from cashier.',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.phone', '+1-555-9999');

        $this->assertDatabaseHas('customers', [
            'id' => $this->customerAlice->id,
            'phone' => '+1-555-9999',
        ]);
    }

    public function test_owner_can_delete_customer(): void
    {
        $response = $this->actingAs($this->owner)->deleteJson("/api/customers/{$this->customerAlice->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('customers', ['id' => $this->customerAlice->id]);
    }

    public function test_manager_cannot_delete_customer(): void
    {
        $response = $this->actingAs($this->manager)->deleteJson("/api/customers/{$this->customerAlice->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('customers', ['id' => $this->customerAlice->id]);
    }

    public function test_cashier_cannot_delete_customer(): void
    {
        $response = $this->actingAs($this->cashier)->deleteJson("/api/customers/{$this->customerAlice->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('customers', ['id' => $this->customerAlice->id]);
    }

    public function test_customer_validation_fails_when_name_or_phone_missing(): void
    {
        $response = $this->actingAs($this->owner)->postJson('/api/customers', [
            'email' => 'invalid-email',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'phone', 'email']);
    }
}
