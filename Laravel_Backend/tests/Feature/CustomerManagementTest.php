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
    private Customer $customerKasun;
    private Customer $customerDinuka;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::create([
            'name' => 'Kaveen Silva',
            'email' => 'owner@creamy.com',
            'password' => Hash::make('Owner@123'),
            'role' => UserRole::OWNER,
        ]);

        $this->manager = User::create([
            'name' => 'Dinuka Jayawardena',
            'email' => 'manager1@creamy.com',
            'password' => Hash::make('Manager1@123'),
            'role' => UserRole::MANAGER,
        ]);

        $this->cashier = User::create([
            'name' => 'Thilini Fernando',
            'email' => 'cashier1@creamy.com',
            'password' => Hash::make('cashier1@123'),
            'role' => UserRole::CASHIER,
        ]);

        $this->customerKasun = Customer::create([
            'name' => 'Kasun Perera',
            'email' => 'kasun.p@gmail.com',
            'phone' => '+94 77 123 4567',
            'address' => 'No. 45, Galle Road, Colombo 03',
            'notes' => 'Prefers gluten-free waffle cones.',
        ]);

        $this->customerDinuka = Customer::create([
            'name' => 'Dinuka Jayasuriya',
            'email' => 'dinuka.j@gmail.com',
            'phone' => '+94 71 234 5678',
            'address' => '12/A, Peradeniya Road, Kandy',
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
            $response = $this->actingAs($user)->getJson("/api/customers/{$this->customerKasun->id}");
            $response->assertOk()
                ->assertJsonPath('data.name', 'Kasun Perera');
        }
    }

    public function test_searching_customers_by_name_or_phone(): void
    {
        $response = $this->actingAs($this->cashier)->getJson('/api/customers?search=Kasun');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Kasun Perera');

        $phoneResponse = $this->actingAs($this->cashier)->getJson('/api/customers?search=5678');
        $phoneResponse->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Dinuka Jayasuriya');
    }

    public function test_all_roles_can_create_customer(): void
    {
        // 1. Owner creates customer
        $respOwner = $this->actingAs($this->owner)->postJson('/api/customers', [
            'name' => 'Thilini Fernando',
            'email' => 'thilini.f@yahoo.com',
            'phone' => '+94 76 345 6789',
        ]);
        $respOwner->assertCreated();

        // 2. Manager creates customer
        $respManager = $this->actingAs($this->manager)->postJson('/api/customers', [
            'name' => 'Sivakumar Tharmarajah',
            'email' => 'sivakumar.t@outlook.com',
            'phone' => '+94 75 456 7890',
        ]);
        $respManager->assertCreated();

        // 3. Cashier creates customer
        $respCashier = $this->actingAs($this->cashier)->postJson('/api/customers', [
            'name' => 'Fathima Riffai',
            'email' => 'fathima.riffai@gmail.com',
            'phone' => '+94 70 567 8901',
        ]);
        $respCashier->assertCreated();

        $this->assertDatabaseCount('customers', 5);
    }

    public function test_all_roles_can_update_customer(): void
    {
        $response = $this->actingAs($this->cashier)->putJson("/api/customers/{$this->customerKasun->id}", [
            'phone' => '+94 77 999 8888',
            'notes' => 'Updated notes from cashier.',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.phone', '+94 77 999 8888');

        $this->assertDatabaseHas('customers', [
            'id' => $this->customerKasun->id,
            'phone' => '+94 77 999 8888',
        ]);
    }

    public function test_owner_can_delete_customer(): void
    {
        $response = $this->actingAs($this->owner)->deleteJson("/api/customers/{$this->customerKasun->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('customers', ['id' => $this->customerKasun->id]);
    }

    public function test_manager_cannot_delete_customer(): void
    {
        $response = $this->actingAs($this->manager)->deleteJson("/api/customers/{$this->customerKasun->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('customers', ['id' => $this->customerKasun->id]);
    }

    public function test_cashier_cannot_delete_customer(): void
    {
        $response = $this->actingAs($this->cashier)->deleteJson("/api/customers/{$this->customerKasun->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('customers', ['id' => $this->customerKasun->id]);
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
