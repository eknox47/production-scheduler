<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('loads the production schedule page', function () {
    $customer = Customer::create([
        'name' => 'Acme Works',
        'email' => 'hello@acme.example',
    ]);

    $productType = ProductType::create([
        'production_speed' => 1000,
    ]);

    $product = Product::create([
        'name' => 'Widget',
        'product_type_id' => $productType->id,
    ]);

    $order = Order::create([
        'customer_id' => $customer->id,
        'need_by_date' => '2026-10-08',
    ]);

    $order->items()->create([
        'product_id' => $product->id,
        'quantity' => 2,
    ]);

    $response = $this->get('/');

    $response->assertOk();
    $response->assertSeeText('Production Schedule');
    $response->assertSeeText('Acme Works');
});

it('creates an order when all products belong to the same product type', function () {
    $customer = Customer::create([
        'name' => 'Test Customer',
        'email' => 'customer@example.com',
    ]);

    $productType = ProductType::create([
        'production_speed' => 1200,
    ]);

    $productA = Product::create([
        'name' => 'Product A',
        'product_type_id' => $productType->id,
    ]);

    $productB = Product::create([
        'name' => 'Product B',
        'product_type_id' => $productType->id,
    ]);

    $response = $this->post('/orders', [
        'customer_id' => $customer->id,
        'need_by_date' => '2026-10-09',
        'items' => [
            ['product_id' => $productA->id, 'quantity' => 3],
            ['product_id' => $productB->id, 'quantity' => 5],
        ],
    ]);

    $response->assertRedirect(route('orders.index'));
    $this->assertDatabaseHas('orders', [
        'customer_id' => $customer->id,
        'need_by_date' => '2026-10-09',
    ]);
    $this->assertDatabaseHas('order_items', [
        'product_id' => $productA->id,
        'quantity' => 3,
    ]);
    $this->assertDatabaseHas('order_items', [
        'product_id' => $productB->id,
        'quantity' => 5,
    ]);
});

it('rejects an order when products belong to different product types', function () {
    $customer = Customer::create([
        'name' => 'Mixed Product Customer',
        'email' => 'mixed@example.com',
    ]);

    $firstType = ProductType::create(['production_speed' => 500]);
    $secondType = ProductType::create(['production_speed' => 700]);

    $productA = Product::create([
        'name' => 'Alpha',
        'product_type_id' => $firstType->id,
    ]);

    $productB = Product::create([
        'name' => 'Beta',
        'product_type_id' => $secondType->id,
    ]);

    $response = $this->from('/orders/create')->post('/orders', [
        'customer_id' => $customer->id,
        'need_by_date' => '2026-10-10',
        'items' => [
            ['product_id' => $productA->id, 'quantity' => 2],
            ['product_id' => $productB->id, 'quantity' => 4],
        ],
    ]);

    $response->assertRedirect('/orders/create');
    $response->assertSessionHasErrors(['items']);
    $this->assertDatabaseCount('orders', 0);
});

it('requires a customer, date, and at least one order item', function () {
    $response = $this->from('/orders/create')->post('/orders', [
        'customer_id' => null,
        'need_by_date' => null,
        'items' => [],
    ]);

    $response->assertRedirect('/orders/create');
    $response->assertSessionHasErrors(['customer_id', 'need_by_date', 'items']);
    $this->assertDatabaseCount('orders', 0);
});

it('rejects duplicate product IDs within the same order', function () {
    $customer = Customer::create([
        'name' => 'Duplicate Customer',
        'email' => 'duplicate@example.com',
    ]);

    $productType = ProductType::create(['production_speed' => 800]);
    $product = Product::create([
        'name' => 'Duplicate Product',
        'product_type_id' => $productType->id,
    ]);

    $response = $this->from('/orders/create')->post('/orders', [
        'customer_id' => $customer->id,
        'need_by_date' => '2026-10-11',
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2],
            ['product_id' => $product->id, 'quantity' => 3],
        ],
    ]);

    $response->assertRedirect('/orders/create');
    $response->assertSessionHasErrors(['items.1.product_id']);
    $this->assertDatabaseCount('orders', 0);
});

it('shows orders sorted by need-by date', function () {
    $customer = Customer::create([
        'name' => 'Sort Customer',
        'email' => 'sort@example.com',
    ]);

    $productType = ProductType::create(['production_speed' => 900]);
    $product = Product::create([
        'name' => 'Sort Product',
        'product_type_id' => $productType->id,
    ]);

    $laterOrder = Order::create([
        'customer_id' => $customer->id,
        'need_by_date' => '2026-10-12',
    ]);
    $laterOrder->items()->create(['product_id' => $product->id, 'quantity' => 1]);

    $earlierOrder = Order::create([
        'customer_id' => $customer->id,
        'need_by_date' => '2026-10-07',
    ]);
    $earlierOrder->items()->create(['product_id' => $product->id, 'quantity' => 1]);

    $response = $this->get('/orders?sort=asc');

    $response->assertOk();
    $response->assertViewHas('sort', 'asc');
    $response->assertViewHas('orders', function ($orders) use ($earlierOrder, $laterOrder) {
        return $orders->first()->id === $earlierOrder->id
            && $orders->last()->id === $laterOrder->id;
    });
});
