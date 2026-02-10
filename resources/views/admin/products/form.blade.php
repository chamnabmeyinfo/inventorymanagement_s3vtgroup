@extends('layouts.admin')

@section('title', $product ? 'Edit product' : 'New product')

@section('content')
<div class="card">
    <h2 style="margin-top: 0;">{{ $product ? 'Edit product' : 'New product' }}</h2>
    <form method="POST" action="{{ $product ? route('admin.products.update', $product) : route('admin.products.store') }}">
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
        <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
