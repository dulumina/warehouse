<?php

namespace Tests\Feature\StockTransaction;

use Tests\TestCase;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\Category;
use App\Models\Unit;
use Spatie\Permission\Models\Role;

class StockInTest extends TestCase
{
    protected User $user;
    protected Warehouse $warehouse;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Warehouse Manager']);
        $this->user = User::factory()->create();
        $this->user->assignRole($role);

        $category = Category::factory()->create();
        $unit = Unit::factory()->create();

        $this->warehouse = Warehouse::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $category->id,
            'unit_id' => $unit->id,
        ]);
    }

    public function test_can_create_stock_in()
    {
        $data = [
            'warehouse_id' => $this->warehouse->id,
            'type' => 'PURCHASE',
            'transaction_date' => now()->date(),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 100,
                    'unit_cost' => 50000,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/stock-ins', $data);

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'DRAFT');
    }

    public function test_can_approve_stock_in()
    {
        $stockIn = StockIn::factory()->create([
            'warehouse_id' => $this->warehouse->id,
            'status' => 'PENDING',
            'received_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/stock-ins/{$stockIn->id}/approve");

        $response->assertOk();
        $this->assertDatabaseHas('stock_ins', [
            'id' => $stockIn->id,
            'status' => 'APPROVED',
        ]);
    }

    public function test_can_reject_stock_in()
    {
        $stockIn = StockIn::factory()->create([
            'warehouse_id' => $this->warehouse->id,
            'status' => 'PENDING',
            'received_by' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/v1/stock-ins/{$stockIn->id}/reject");

        $response->assertOk();
        $this->assertDatabaseHas('stock_ins', [
            'id' => $stockIn->id,
            'status' => 'REJECTED',
        ]);
    }
}
