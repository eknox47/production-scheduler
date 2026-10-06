@extends('layouts.app')

@section('title', 'Orders')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Orders</h2>

        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            + Create Order
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>
                            <a href="{{ route('orders.index', ['sort' => $sort === 'asc' ? 'desc' : 'asc']) }}"
                            class="text-white text-decoration-none">
                                Need By
                                @if ($sort === 'asc')
                                    ↑
                                @else
                                    ↓
                                @endif
                            </a>
                        </th>
                        <th>Products</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td class="fw-semibold">
                                #{{ $order->id }}
                            </td>

                            <td>
                                {{ $order->customer->name }}
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($order->need_by_date)->format('M d, Y') }}
                            </td>

                            <td>
                                @foreach ($order->items as $item)
                                    <span class="badge text-bg-light border me-1">
                                        {{ $item->product->name }}
                                        × {{ $item->quantity }}
                                    </span>
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection