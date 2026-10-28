<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ItemManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $manager;
    private User $cashier;
    private Item $chocolateItem;
    private Item $vanillaItem;

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

        $this->chocolateItem = Item::create([
            'name' => 'Belgian Dark Chocolate Supreme',
            'flavor_category' => 'Chocolate',
            'price' => 650.00,
            'quantity' => 100,
            'unit' => 'scoop',
            'description' => 'Rich dark cocoa ice cream.',
            'is_active' => true,
        ]);

        $this->vanillaItem = Item::create([
            'name' => 'Madagascar Vanilla Bean',
            'flavor_category' => 'Vanilla',
            'price' => 550.00,
            'quantity' => 150,
            'unit' => 'scoop',
            'description' => 'Classic vanilla bean flavor.',
            'is_active' => true,
        ]);
    }

    public function test_all_roles_can_view_items(): void
    {
        foreach ([$this->owner, $this->manager, $this->cashier] as $user) {
            $response = $this->actingAs($user)->getJson('/api/items');
            $response->assertOk()
                ->assertJsonCount(2, 'data');
        }
    }

    public function test_all_roles_can_view_single_item(): void
    {
        foreach ([$this->owner, $this->manager, $this->cashier] as $user) {
            $response = $this->actingAs($user)->getJson("/api/items/{$this->chocolateItem->id}");
            $response->assertOk()
                ->assertJsonPath('data.name', 'Belgian Dark Chocolate Supreme');
        }
    }

    public function test_searching_items_by_name_returns_filtered_results(): void
    {
        $response = $this->actingAs($this->cashier)->getJson('/api/items?search=Chocolate');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Belgian Dark Chocolate Supreme');
    }

    public function test_filtering_items_by_flavor_category(): void
    {
        $response = $this->actingAs($this->cashier)->getJson('/api/items?category=Vanilla');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.flavor_category', 'Vanilla');
    }

    public function test_owner_can_create_an_item(): void
    {
        $payload = [
            'name' => 'Wild Strawberry Swirl',
            'flavor_category' => 'Strawberry',
            'price' => 600.00,
            'quantity' => 60,
            'unit' => 'scoop',
            'description' => 'Fresh strawberry flavor.',
            'is_active' => true,
        ];

        $response = $this->actingAs($this->owner)->postJson('/api/items', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Wild Strawberry Swirl');

        $this->assertDatabaseHas('items', ['name' => 'Wild Strawberry Swirl']);
    }

    public function test_manager_can_create_an_item(): void
    {
        $payload = [
            'name' => 'Mango Passionfruit Sorbet',
            'flavor_category' => 'Fruit',
            'price' => 650.00,
            'quantity' => 45,
            'unit' => 'scoop',
        ];

        $response = $this->actingAs($this->manager)->postJson('/api/items', $payload);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Mango Passionfruit Sorbet');

        $this->assertDatabaseHas('items', ['name' => 'Mango Passionfruit Sorbet']);
    }

    public function test_cashier_cannot_create_an_item(): void
    {
        $payload = [
            'name' => 'Salted Caramel Crunch',
            'flavor_category' => 'Caramel',
            'price' => 750.00,
            'quantity' => 30,
        ];

        $response = $this->actingAs($this->cashier)->postJson('/api/items', $payload);

        $response->assertForbidden();
        $this->assertDatabaseMissing('items', ['name' => 'Salted Caramel Crunch']);
    }

    public function test_all_roles_can_update_an_existing_item(): void
    {
        // Cashier updates quantity (e.g., after serving customer)
        $response = $this->actingAs($this->cashier)->putJson("/api/items/{$this->chocolateItem->id}", [
            'quantity' => 88,
        ]);

        $response->assertOk()
            ->assertJsonPath('data.quantity', 88);

        $this->assertDatabaseHas('items', [
            'id' => $this->chocolateItem->id,
            'quantity' => 88,
        ]);
    }

    public function test_owner_can_delete_an_item(): void
    {
        $response = $this->actingAs($this->owner)->deleteJson("/api/items/{$this->chocolateItem->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('items', ['id' => $this->chocolateItem->id]);
    }

    public function test_manager_cannot_delete_an_item(): void
    {
        $response = $this->actingAs($this->manager)->deleteJson("/api/items/{$this->chocolateItem->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('items', ['id' => $this->chocolateItem->id]);
    }

    public function test_cashier_cannot_delete_an_item(): void
    {
        $response = $this->actingAs($this->cashier)->deleteJson("/api/items/{$this->chocolateItem->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('items', ['id' => $this->chocolateItem->id]);
    }

    public function test_item_validation_fails_for_invalid_payload(): void
    {
        $response = $this->actingAs($this->owner)->postJson('/api/items', [
            'name' => '',
            'price' => -5,
            'quantity' => -10,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'flavor_category', 'price', 'quantity']);
    }
}
