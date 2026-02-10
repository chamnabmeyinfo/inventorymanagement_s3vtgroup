@extends('layouts.admin')

@section('title', 'Stock history')

@section('content')
<div class="card">
    <h2 style="margin-top: 0;">Stock history: {{ $product->name }} <span style="font-weight: normal; color: #64748b;">({{ $product->sku }})</span></h2>
    <p>Current: <strong>{{ $product->stock?->quantity ?? 0 }}</strong> units – <span class="badge badge-{{ $product->stock?->status ?? 'out_of_stock' }}">{{ str_replace('_', ' ', $product->stock?->status ?? 'out_of_stock') }}</span></p>
    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-secondary">Edit product</a>
</div>
<div class="card">
    <h3 style="margin-top: 0;">Movements</h3>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Supplier</th>
                <th>Reference</th>
                <th>Notes</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                    <td><span class="badge badge-{{ $m->type === 'in' ? 'in_stock' : ($m->type === 'out' ? 'out_of_stock' : 'on_order') }}">{{ $m->type }}</span></td>
                    <td>{{ $m->quantity }}</td>
                    <td>@if($m->supplier)<a href="{{ route('admin.suppliers.edit', $m->supplier) }}">{{ $m->supplier->name }}</a>@else—@endif</td>
                    <td>{{ $m->reference ?? '—' }}</td>
                    <td>{{ $m->notes ?? '—' }}</td>
                    <td>{{ $m->user?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No movements yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
