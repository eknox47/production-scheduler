<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ScheduleController::class, 'schedule'])
    ->name('schedule');

Route::get('/orders', [OrderController::class, 'index'])
    ->name('orders.index');

Route::post('/orders', [OrderController::class, 'store'])
    ->name('orders.store');

Route::get('/orders/create', [OrderController::class, 'create'])
    ->name('orders.create');