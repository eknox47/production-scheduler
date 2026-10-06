@extends('layouts.app')

@section('title', 'Production Schedule')

@section('content')
    <h2 class="mb-4">Production Schedule</h2>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Product Type</th>
                        <th>Start</th>
                        <th>End</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($schedule as $item)
                        <tr>
                            <td>#{{ $item['order']->id }}</td>
                            <td>{{ $item['order']->customer->name }}</td>
                            <td>{{ $item['product_type']->id }}</td>
                            <td>{{ $item['start']->format('M d, Y H:i') }}</td>
                            <td>{{ $item['end']->format('M d, Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection