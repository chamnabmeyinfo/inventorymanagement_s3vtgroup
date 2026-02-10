@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="card" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
    <h2 style="margin: 0; flex: 1 1 100%;">Products</h2>
    <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Category</label>
            <select name="category_id">
                <option value="">All</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label>Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Name or SKU">
        </div>
        <button type="submit" class="btn btn-secondary">Filter</button>
    </form>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add product</a>
</div>
<div class="card">
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Name</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Stock</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $p)
                <tr>
                    <td>{{ $p->sku }}</td>
                    <td>{{ $p->name }}</td>
                    <td>{{ $p->category?->name ?? '—' }}</td>
                    <td>@if($p->preferredSupplier)<a href="{{ route('admin.suppliers.edit', $p->preferredSupplier) }}">{{ $p->preferredSupplier->name }}</a>@else—@endif</td>
                    <td>{{ $p->stock?->quantity ?? 0 }}</td>
                    <td><span class="badge badge-{{ $p->stock?->status ?? 'out_of_stock' }}">{{ str_replace('_', ' ', $p->stock?->status ?? 'out_of_stock') }}</span></td>
                    <td>
                        <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.875rem;">Edit</a>
                        <a href="{{ route('admin.stock-movements.history', $p) }}" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.875rem;">History</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $products->withQueryString()->links() }}
</div>
@endsection
