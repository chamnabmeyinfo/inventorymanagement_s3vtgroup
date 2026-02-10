@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="card">
    <h2 style="margin-top: 0;">Reports</h2>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
        <a href="{{ route('admin.reports.index', ['tab' => 'out-of-stock']) }}" class="btn {{ $tab === 'out-of-stock' ? 'btn-primary' : 'btn-secondary' }}">Out of stock</a>
        <a href="{{ route('admin.reports.index', ['tab' => 'low-stock']) }}" class="btn {{ $tab === 'low-stock' ? 'btn-primary' : 'btn-secondary' }}">Low stock</a>
        <a href="{{ route('admin.reports.index', ['tab' => 'movements']) }}" class="btn {{ $tab === 'movements' ? 'btn-primary' : 'btn-secondary' }}">Movement history</a>
    </div>
    @if($tab === 'low-stock')
        <form method="GET" style="display: flex; gap: 1rem; align-items: flex-end; margin-bottom: 1rem;">
            <input type="hidden" name="tab" value="low-stock">
            <div class="form-group" style="margin-bottom: 0; max-width: 200px;">
                <label>Quantity ≤ (threshold)</label>
                <input type="number" min="0" name="threshold" value="{{ request('threshold', 0) }}">
            </div>
            <button type="submit" class="btn btn-primary">Apply</button>
        </form>
    @endif
    @if($tab === 'movements')
        <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; margin-bottom: 1rem;">
            <input type="hidden" name="tab" value="movements">
            <div class="form-group" style="margin-bottom: 0;">
                <label>From date</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label>To date</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}">
            </div>
            <button type="submit" class="btn btn-primary">Apply</button>
        </form>
    @endif
</div>
<div class="card">
    @if($tab === 'out-of-stock')
        <table>
            <thead>
                <tr><th>SKU</th><th>Name</th><th>Category</th><th>Quantity</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($outOfStock as $p)
                    <tr>
                        <td>{{ $p->sku }}</td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->category?->name ?? '—' }}</td>
                        <td>{{ $p->stock?->quantity ?? 0 }}</td>
                        <td><a href="{{ route('admin.products.edit', $p) }}" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.875rem;">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5">No out-of-stock products.</td></tr>
                @endforelse
            </tbody>
        </table>
    @elseif($tab === 'low-stock')
        <table>
            <thead>
                <tr><th>SKU</th><th>Name</th><th>Category</th><th>Quantity</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($lowStock as $p)
                    <tr>
                        <td>{{ $p->sku }}</td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->category?->name ?? '—' }}</td>
                        <td>{{ $p->stock?->quantity ?? 0 }}</td>
                        <td><span class="badge badge-{{ $p->stock?->status ?? 'out_of_stock' }}">{{ str_replace('_', ' ', $p->stock?->status ?? 'out_of_stock') }}</span></td>
                        <td><a href="{{ route('admin.products.edit', $p) }}" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.875rem;">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6">No products at or below threshold.</td></tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr><th>Date</th><th>Product</th><th>SKU</th><th>Type</th><th>Quantity</th><th>Reference</th></tr>
            </thead>
            <tbody>
                @forelse($movements as $m)
                    <tr>
                        <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                        <td><a href="{{ route('admin.products.edit', $m->product) }}">{{ $m->product->name }}</a></td>
                        <td>{{ $m->product->sku }}</td>
                        <td>{{ $m->type }}</td>
                        <td>{{ $m->quantity }}</td>
                        <td>{{ $m->reference ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">No movements in range. Set dates and click Apply.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif
</div>
@endsection
