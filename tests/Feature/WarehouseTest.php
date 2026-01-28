<?php

namespace Tests\Feature\Warehouse;

use Tests\TestCase;
use App\Models\User;
use App\Models\Warehouse;
use Spatie\Permission\Models\Role;

class WarehouseTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create role and user
        $role = Role::firstOrCreate(['name' => 'Warehouse Manager']);
        $this->user = User::factory()->create();
        $this->user->assignRole($role);
    }

    public function test_can_list_warehouses()
    {
        $warehouses = Warehouse::factory(3)->create();

        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/warehouses');

        $response->assertOk()
            ->assertJsonStructure(['success', 'data', 'message']);
    }

    public function test_can_create_warehouse()
    {
        $data = [
            'code' => 'WH001',
            'name' => 'Main Warehouse',
            'address' => '123 Storage Lane',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '12345',
            'phone' => '+62212345678',
            'email' => 'warehouse@example.com',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/warehouses', $data);

        $response->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('warehouses', ['code' => 'WH001']);
    }

    public function test_can_view_warehouse_detail()
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/warehouses/{$warehouse->id}");

        $response->assertOk()
            ->assertJsonPath('data.code', $warehouse->code);
    }

    public function test_can_update_warehouse()
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/warehouses/{$warehouse->id}", [
                'name' => 'Updated Warehouse',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('warehouses', [
            'id' => $warehouse->id,
            'name' => 'Updated Warehouse',
        ]);
    }

    public function test_can_delete_warehouse()
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/warehouses/{$warehouse->id}");

        $response->assertOk();
        $this->assertSoftDeleted('warehouses', ['id' => $warehouse->id]);
    }
}
