<div class="product-item border rounded p-3 mb-3">
    <div class="mb-3">
        <label class="form-label">Product</label>

        <select name="items[{{ $index }}][product_id]"
                class="form-select product-select"
                required>
            <option value="">Select a product</option>

            @foreach ($products as $product)
                <option value="{{ $product->id }}"
                        data-type="{{ $product->product_type_id }}">
                    {{ $product->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Quantity</label>

        <input
            type="number"
            name="items[{{ $index }}][quantity]"
            class="form-control"
            min="1"
            required
        >
    </div>

    <button type="button" class="btn btn-outline-danger remove-product">
        Remove
    </button>
</div>