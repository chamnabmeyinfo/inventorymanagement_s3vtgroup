@extends('layouts.admin')

@section('title', $product ? 'Edit product' : 'New product')

@section('content')
<div class="card">
    <h2 style="margin-top: 0;">{{ $product ? 'Edit product' : 'New product' }}</h2>
    <form method="POST" action="{{ $product ? route('admin.products.update', $product) : route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        @if($product) @method('PUT') @endif
        <div class="form-group">
            <label>SKU *</label>
            <input name="sku" value="{{ old('sku', $product?->sku) }}" required>
        </div>
        <div class="form-group">
            <label>Name *</label>
            <input name="name" value="{{ old('name', $product?->name) }}" required>
        </div>
        <div class="form-group">
            <label>Slug</label>
            <input name="slug" value="{{ old('slug', $product?->slug) }}" placeholder="auto from name">
        </div>
        <div class="form-group">
            <label>Category</label>
            <select name="category_id">
                <option value="">—</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ old('category_id', $product?->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        @if(isset($suppliers) && $suppliers->isNotEmpty())
        <div class="form-group">
            <label>Preferred supplier</label>
            <select name="preferred_supplier_id">
                <option value="">—</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ old('preferred_supplier_id', $product?->preferred_supplier_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
            <span style="font-size: 0.8125rem; color: #64748b;">Suggested for reorder alerts</span>
        </div>
        @endif
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3">{{ old('description', $product?->description) }}</textarea>
        </div>
        <div class="form-group">
            <label>Price display</label>
            <select name="price_display_type">
                <option value="on_request" {{ old('price_display_type', $product?->price_display_type ?? 'on_request') == 'on_request' ? 'selected' : '' }}>Price on request</option>
                <option value="fixed" {{ old('price_display_type', $product?->price_display_type) == 'fixed' ? 'selected' : '' }}>Fixed price</option>
            </select>
        </div>
        <div class="form-group">
            <label>Price amount</label>
            <input type="number" step="0.01" min="0" name="price_amount" value="{{ old('price_amount', $product?->price_amount) }}">
        </div>
        <div class="form-group">
            <label>Stock status</label>
            <select name="stock_status">
                <option value="in_stock" {{ old('stock_status', $product?->stock?->status ?? 'out_of_stock') == 'in_stock' ? 'selected' : '' }}>In stock</option>
                <option value="on_order" {{ old('stock_status', $product?->stock?->status) == 'on_order' ? 'selected' : '' }}>On order</option>
                <option value="out_of_stock" {{ old('stock_status', $product?->stock?->status ?? 'out_of_stock') == 'out_of_stock' ? 'selected' : '' }}>Out of stock</option>
            </select>
        </div>
        <div class="form-group">
            <label>Stock quantity</label>
            <input type="number" min="0" name="stock_quantity" value="{{ old('stock_quantity', $product?->stock?->quantity ?? 0) }}">
        </div>
        <div class="form-group">
            <label>Reorder point</label>
            <input type="number" min="0" name="reorder_point" value="{{ old('reorder_point', $product?->reorder_point) }}" placeholder="Alert when stock ≤ this (blank = use default)">
        </div>
        <div class="form-group">
            <label>Images</label>
            <div id="existing-images" style="display: flex; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1rem;">
                @foreach($existingImages ?? [] as $path)
                    <div class="img-item" style="position: relative; width: 100px; height: 100px;">
                        <img src="{{ asset('storage/' . $path) }}" alt="" style="width: 100%; height: 100%; object-fit: cover; border-radius: 6px; border: 1px solid #e2e8f0;">
                        <input type="hidden" name="existing_images[]" value="{{ $path }}">
                        <button type="button" class="img-remove btn btn-danger" style="position: absolute; top: 2px; right: 2px; padding: 0.2rem 0.4rem; font-size: 0.75rem; line-height: 1;">✕</button>
                    </div>
                @endforeach
            </div>
            <div id="new-images">
                <input type="file" name="images[]" accept="image/jpeg,image/png,image/gif,image/webp" class="img-file" style="display: block; margin-bottom: 0.5rem;">
            </div>
            <button type="button" id="add-image-btn" class="btn btn-secondary" style="margin-top: 0.5rem;">Add more images</button>
            <small style="display: block; color: #64748b; margin-top: 0.25rem;">JPG, PNG, GIF, WebP. Max 5MB each.</small>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-image-btn').addEventListener('click', function() {
        var div = document.getElementById('new-images');
        var inp = document.createElement('input');
        inp.type = 'file';
        inp.name = 'images[]';
        inp.accept = 'image/jpeg,image/png,image/gif,image/webp';
        inp.className = 'img-file';
        inp.style.marginTop = '0.5rem';
        inp.style.marginBottom = '0.5rem';
        inp.style.display = 'block';
        div.appendChild(inp);
    });
    document.getElementById('existing-images').addEventListener('click', function(e) {
        if (e.target.classList.contains('img-remove')) {
            e.target.closest('.img-item').remove();
        }
    });
});
</script>
@endpush
@endsection
