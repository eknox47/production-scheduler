<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->get('sort', 'asc');

        $orders = Order::with(['customer', 'items.product'])
            ->orderBy('need_by_date', $sort)
            ->get();

        return view('orders.index', compact('orders', 'sort'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $products = Product::with('productType')->get();

        return view('orders.create', compact('customers', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'need_by_date' => ['required', 'date'],
        ]);

        $productIds = collect($validated['items'])
            ->pluck('product_id');

        $productTypes = Product::whereIn('id', $productIds)
            ->pluck('product_type_id')
            ->unique();

        if ($productTypes->count() > 1) {
            return back()
                ->withErrors([
                    'items' => 'All products in an order must have the same product type.',
                ])
                ->withInput();
        }

        $order = DB::transaction(function () use ($validated) {
            $order = Order::create([
                'customer_id' => $validated['customer_id'],
                'need_by_date' => $validated['need_by_date'],
            ]);

            foreach ($validated['items'] as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);
            }

            return $order;
        });

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order created successfully.');
    }
}
