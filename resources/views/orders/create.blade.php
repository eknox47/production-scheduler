@extends('layouts.app')

@section('title', 'Create Order')

@section('content')
    <div class="col-md-6 mx-auto">

        <h1 class="mb-4">Create Order</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>

                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('orders.store') }}">
            @csrf
            <div class="mb-3">
                <label for="customer" class="form-label">Customer</label>

                <select name="customer_id" id="customer" class="form-select" required>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}"
                            {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="need_by_date" class="form-label">Need by</label>
                <input
                    type="date"
                    name="need_by_date"
                    id="need_by_date"
                    class="form-control"
                    min="{{ date('Y-m-d') }}"
                    value="{{ old('need_by_date') }}"
                    required
                >
            </div>

            <div id="products">
                @include('orders._product-item', [
                    'products' => $products,
                    'index' => 0
                ])
            </div>

            <button type="button" id="add-product" class="btn btn-outline-primary mb-4">
                + Add Product
            </button>

            <div>
                <button type="submit" class="btn btn-primary">
                    Create Order
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    const products = @json($products);
    const productsContainer = document.getElementById('products');
    const addProductButton = document.getElementById('add-product');

    let productIndex = 1;

    addProductButton.addEventListener('click', function () {
        const div = document.createElement('div');

        div.classList.add('product-item', 'border', 'rounded', 'p-3', 'mb-3');

        div.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Product</label>

                <select name="items[${productIndex}][product_id]"
                        class="form-select product-select"
                        required>
                    <option value="">Select a product</option>

                    ${products.map(product => `
                        <option value="${product.id}"
                                data-type="${product.product_type_id}">
                            ${product.name}
                        </option>
                    `).join('')}
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity</label>

                <input
                    type="number"
                    name="items[${productIndex}][quantity]"
                    class="form-control"
                    min="1"
                    required
                >
            </div>

            <button type="button" class="btn btn-outline-danger remove-product">
                Remove
            </button>
        `;

        productsContainer.appendChild(div);

        productIndex++;

        updateProductOptions();
    });

    productsContainer.addEventListener('change', function (event) {
        if (event.target.classList.contains('product-select')) {
            updateProductOptions();
        }
    });

    productsContainer.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-product')) {
            event.target.closest('.product-item').remove();

            updateProductOptions();
        }
    });

    function updateProductOptions() {
        const selects = document.querySelectorAll('.product-select');

        const selectedProducts = Array.from(selects)
            .map(select => select.value)
            .filter(value => value !== '');

        const selectedTypes = Array.from(selects)
            .map(select => {
                const option = select.options[select.selectedIndex];
                return option?.dataset.type;
            })
            .filter(type => type);

        const orderType = selectedTypes[0];

        selects.forEach(select => {
            const currentValue = select.value;

            Array.from(select.options).forEach(option => {
                if (!option.value) {
                    return;
                }

                const alreadySelected =
                    selectedProducts.includes(option.value) &&
                    option.value !== currentValue;

                const differentType =
                    orderType &&
                    option.dataset.type !== orderType;

                option.hidden = alreadySelected || differentType;
            });
        });
    }
</script>
@endpush