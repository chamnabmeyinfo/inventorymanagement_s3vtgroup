@extends('layouts.admin')

@section('title', 'Reports')

@push('styles')
<style>
    .report-summary { display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
    .report-summary .card { padding: 1rem; text-align: center; }
    .report-summary .card .num { font-size: 1.5rem; font-weight: 700; }
    .report-table { font-size: 0.9375rem; }
    .report-table th { white-space: nowrap; }
    .report-table td a { font-weight: 500; }
    .report-section { margin-bottom: 2rem; }
    .report-tabs { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-bottom: 1rem; }
    .report-filters { display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; margin-bottom: 1rem; padding: 1rem; background: #f8fafc; border-radius: 8px; }
    .report-filters .form-group { margin-bottom: 0; }
    .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    @media (max-width: 640px) { .report-summary { grid-template-columns: repeat(2, 1fr); } }
</style>
@endpush

@section('content')
<div class="card">
    <h2 style="margin-top: 0;">Detail reports</h2>
    <p style="color: #64748b; margin: 0 0 1rem 0; font-size: 0.9375rem;">Stock status, low stock alerts, and movement history.</p>

    <div class="report-tabs">
        <a href="{{ route('admin.reports.index', ['tab' => 'out-of-stock']) }}" class="btn {{ $tab === 'out-of-stock' ? 'btn-primary' : 'btn-secondary' }}">Out of stock</a>
        <a href="{{ route('admin.reports.index', ['tab' => 'low-stock']) }}" class="btn {{ $tab === 'low-stock' ? 'btn-primary' : 'btn-secondary' }}">Low stock</a>
        <a href="{{ route('admin.reports.index', ['tab' => 'movements']) }}" class="btn {{ $tab === 'movements' ? 'btn-primary' : 'btn-secondary' }}">Movement history</a>
        <a href="{{ route('admin.reports.export', request()->query()) }}" class="btn btn-secondary" style="margin-left: auto;">Export CSV</a>
    </div>

    @if($tab === 'low-stock')
    <form method="GET" class="report-filters">
        <input type="hidden" name="tab" value="low-stock">
        <div class="form-group" style="max-width: 200px;">
            <label>Quantity ≤ (threshold)</label>
            <input type="number" min="0" name="threshold" value="{{ $threshold }}">
        </div>
        <button type="submit" class="btn btn-primary">Apply</button>
    </form>
    @endif

    @if($tab === 'movements')
    <form method="GET" id="movementsForm" class="report-filters">
        <input type="hidden" name="tab" value="movements">
        <div style="display: flex; gap: 0.25rem; flex-wrap: wrap;">
            <a href="{{ route('admin.reports.index', ['tab' => 'movements', 'from_date' => now()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" class="btn btn-secondary" style="padding: 0.4rem 0.6rem; font-size: 0.8125rem;">Today</a>
            <a href="{{ route('admin.reports.index', ['tab' => 'movements', 'from_date' => now()->startOfWeek()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" class="btn btn-secondary" style="padding: 0.4rem 0.6rem; font-size: 0.8125rem;">This week</a>
            <a href="{{ route('admin.reports.index', ['tab' => 'movements', 'from_date' => now()->startOfMonth()->format('Y-m-d'), 'to_date' => now()->format('Y-m-d')]) }}" class="btn btn-secondary" style="padding: 0.4rem 0.6rem; font-size: 0.8125rem;">This month</a>
        </div>
        @if(isset($suppliers) && $suppliers->isNotEmpty())
        <div class="form-group" style="margin-bottom: 0;">
            <label>Supplier</label>
            <select name="supplier_id">
                <option value="">All</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ request('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
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

{{-- Summary cards --}}
<div class="report-summary">
    <div class="card">
        <div class="num" style="color: #0f172a;">{{ $summary['total_products'] }}</div>
        <div style="font-size: 0.875rem; color: #64748b;">Total products</div>
    </div>
    <div class="card">
        <div class="num" style="color: #166534;">{{ $summary['in_stock'] }}</div>
        <div style="font-size: 0.875rem; color: #64748b;">In stock</div>
    </div>
    <div class="card">
        <div class="num" style="color: #dc2626;">{{ $summary['out_of_stock'] }}</div>
        <div style="font-size: 0.875rem; color: #64748b;">Out of stock</div>
    </div>
</div>

{{-- Category breakdown (always visible) --}}
@if($categoryBreakdown->isNotEmpty())
<div class="card report-section">
    <h3 style="margin: 0 0 1rem 0;">By category</h3>
    <div class="table-wrap">
    <table class="report-table">
        <thead>
            <tr><th>Category</th><th>Total</th><th>In stock</th><th>Out of stock</th><th>Status</th></tr>
        </thead>
        <tbody>
            @foreach($categoryBreakdown as $row)
            <tr>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['total'] }}</td>
                <td>{{ $row['in_stock'] }}</td>
                <td>{{ $row['out_of_stock'] }}</td>
                <td>
                    @php
                        $pct = $row['total'] > 0 ? round(100 * $row['in_stock'] / $row['total']) : 0;
                    @endphp
                    <span class="badge {{ $pct >= 80 ? 'badge-in_stock' : ($pct >= 50 ? 'badge-on_order' : 'badge-out_of_stock') }}">{{ $pct }}% in stock</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</div>
@endif

{{-- Tab content --}}
<div class="card report-section">
    @if($tab === 'out-of-stock')
        <h3 style="margin: 0 0 1rem 0;">Out of stock ({{ $outOfStock->count() }} products)</h3>
        <div class="table-wrap">
        <table class="report-table">
            <thead>
                <tr><th>SKU</th><th>Name</th><th>Category</th><th>Quantity</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($outOfStock as $p)
                <tr>
                    <td>{{ $p->sku }}</td>
                    <td><a href="{{ route('admin.products.edit', $p) }}">{{ $p->name }}</a></td>
                    <td>{{ $p->category?->name ?? '—' }}</td>
                    <td>{{ $p->stock?->quantity ?? 0 }}</td>
                    <td>
                        <a href="{{ route('admin.stock-movements.create') }}?product_id={{ $p->id }}" class="btn btn-primary btn-sm">Record stock</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5">No out-of-stock products.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    @elseif($tab === 'low-stock')
        <h3 style="margin: 0 0 1rem 0;">Low stock (≤ {{ $threshold }}) — {{ $lowStock->count() }} products</h3>
        <div class="table-wrap">
        <table class="report-table">
            <thead>
                <tr><th>SKU</th><th>Name</th><th>Category</th><th>Quantity</th><th>Reorder</th><th></th></tr>
            </thead>
            <tbody>
                @forelse($lowStock as $p)
                <tr>
                    <td>{{ $p->sku }}</td>
                    <td><a href="{{ route('admin.products.edit', $p) }}">{{ $p->name }}</a></td>
                    <td>{{ $p->category?->name ?? '—' }}</td>
                    <td><strong>{{ $p->stock?->quantity ?? 0 }}</strong></td>
                    <td>{{ $p->reorder_point ?? '—' }}</td>
                    <td>
                        <a href="{{ route('admin.stock-movements.create') }}?product_id={{ $p->id }}" class="btn btn-primary btn-sm">Record stock</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6">No products at or below threshold.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    @else
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 1rem;">
            <h3 style="margin: 0;">Movement history ({{ $movements->count() }} records)</h3>
            @if($movements->isNotEmpty())
            <div style="font-size: 0.9375rem; color: #64748b;">
                Total: <strong style="color: #166534;">+{{ $movementTotals['in'] ?? 0 }}</strong> in /
                <strong style="color: #dc2626;">-{{ $movementTotals['out'] ?? 0 }}</strong> out
            </div>
            @endif
        </div>
        <div class="table-wrap">
        <table class="report-table">
        <thead>
            <tr><th>Date</th><th>Product</th><th>SKU</th><th>Type</th><th>Quantity</th><th>Supplier</th><th>Reference</th></tr>
        </thead>
        <tbody>
            @forelse($movements as $m)
            <tr>
                <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                <td><a href="{{ route('admin.products.edit', $m->product) }}">{{ $m->product->name }}</a></td>
                <td>{{ $m->product->sku }}</td>
                <td><span class="badge badge-{{ $m->type === 'in' ? 'in_stock' : ($m->type === 'out' ? 'out_of_stock' : 'on_order') }}">{{ $m->type }}</span></td>
                <td>{{ $m->quantity }}</td>
                <td>@if($m->supplier)<a href="{{ route('admin.suppliers.edit', $m->supplier) }}">{{ $m->supplier->name }}</a>@else—@endif</td>
                <td>{{ $m->reference ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="7">No movements in range. Set dates and click Apply.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    @endif
</div>
@endsection
