<?php

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductType;
use App\Services\SchedulerService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

it('schedules orders in chronological order by due date', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-05 08:00:00'));

    $customer = Customer::create([
        'name' => 'Scheduler Customer',
        'email' => 'scheduler@example.com',
    ]);

    $productType = ProductType::create(['production_speed' => 400]);
    $product = Product::create([
        'name' => 'Scheduled Product',
        'product_type_id' => $productType->id,
    ]);

    $latestOrder = Order::create([
        'customer_id' => $customer->id,
        'need_by_date' => '2026-10-12',
    ]);
    $latestOrder->items()->create(['product_id' => $product->id, 'quantity' => 1]);

    $middleOrder = Order::create([
        'customer_id' => $customer->id,
        'need_by_date' => '2026-10-07',
    ]);
    $middleOrder->items()->create(['product_id' => $product->id, 'quantity' => 1]);

    $earliestOrder = Order::create([
        'customer_id' => $customer->id,
        'need_by_date' => '2026-10-06',
    ]);
    $earliestOrder->items()->create(['product_id' => $product->id, 'quantity' => 1]);

    $schedule = (new SchedulerService())->schedule();

    expect($schedule)->toHaveCount(3)
        ->and($schedule[0]['order']->need_by_date)->toBe('2026-10-06')
        ->and($schedule[1]['order']->need_by_date)->toBe('2026-10-07')
        ->and($schedule[2]['order']->need_by_date)->toBe('2026-10-12');
});

it('creates an end time after the start time for every scheduled order', function () {
    Carbon::setTestNow(Carbon::parse('2026-10-05 08:00:00'));

    $customer = Customer::create([
        'name' => 'After Hours Customer',
        'email' => 'afterhours@example.com',
    ]);

    $productType = ProductType::create(['production_speed' => 10]);
    $product = Product::create([
        'name' => 'Long Job',
        'product_type_id' => $productType->id,
    ]);

    $order = Order::create([
        'customer_id' => $customer->id,
        'need_by_date' => '2026-10-08',
    ]);
    $order->items()->create(['product_id' => $product->id, 'quantity' => 1000]);

    $schedule = (new SchedulerService())->schedule();

    expect($schedule)->toHaveCount(1)
        ->and($schedule[0]['start']->isWeekday())->toBeTrue()
        ->and($schedule[0]['start']->hour)->toBe(9)
        ->and($schedule[0]['end']->greaterThan($schedule[0]['start']))->toBeTrue();
});
