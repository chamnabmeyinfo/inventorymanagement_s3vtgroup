@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="card">
    <div class="page-header">
        <h2>Products</h2>
        <div class="btn-group">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label>Category</label>
                    <select name="category_id">
                        <option value="">All</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Search</label>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Name or SKU">
                </div>
                <button type="submit" class="btn btn-secondary">Filter</button>
            </form>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add product</a>
        </div>
    </div>
    <div class="table-wrap">
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
                    <td class="action-cell">
                        <a href="{{ route('admin.products.edit', $p) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <a href="{{ route('admin.stock-movements.history', $p) }}" class="btn btn-secondary btn-sm">History</a>
                        <form action="{{ route('admin.products.duplicate', $p) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Duplicate</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7">No products found.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    {{ $products->withQueryString()->links() }}
</div>
@endsection
