@extends('layouts.admin')

@section('title', 'Record stock movement')

@section('content')
<div class="card">
    <h2 style="margin-top: 0;">Record stock movement</h2>
    <form method="POST" action="{{ route('admin.stock-movements.store') }}">
        @csrf
        <div class="form-group">
            <label>Product *</label>
            <select name="product_id" required>
                <option value="">Select product</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ old('product_id', $preselectedProductId ?? null) == $p->id ? 'selected' : '' }}>{{ $p->sku }} – {{ $p->name }} (qty: {{ $p->stock?->quantity ?? 0 }})</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Type *</label>
            <select name="type" id="movementType">
                <option value="in" {{ old('type', 'in') == 'in' ? 'selected' : '' }}>In</option>
                <option value="out">Out</option>
                <option value="adjustment">Adjustment</option>
                <option value="transfer">Transfer</option>
            </select>
        </div>
        @if(isset($suppliers) && $suppliers->isNotEmpty())
        <div class="form-group" id="supplierGroup">
            <label>Supplier</label>
            <select name="supplier_id">
                <option value="">—</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" {{ old('supplier_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
            <span style="font-size: 0.8125rem; color: #64748b;">Link stock-in to supplier (optional)</span>
        </div>
        @endif
        <div class="form-group">
            <label>Quantity *</label>
            <input type="number" min="1" name="quantity" value="{{ old('quantity', 1) }}" required>
        </div>
        <div class="form-group">
            <label>Reference (PO / order id)</label>
            <input name="reference" value="{{ old('reference') }}" placeholder="Optional">
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" rows="2">{{ old('notes') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Record movement</button>
    </form>
</div>
@if(isset($suppliers) && $suppliers->isNotEmpty())
@push('scripts')
<script>
(function() {
    var typeSelect = document.getElementById('movementType');
    var supplierGroup = document.getElementById('supplierGroup');
    if (supplierGroup) {
        function toggleSupplier() {
            var show = typeSelect && ['in', 'adjustment'].includes(typeSelect.value);
            supplierGroup.style.display = show ? 'block' : 'none';
        }
        if (typeSelect) { typeSelect.addEventListener('change', toggleSupplier); toggleSupplier(); }
    }
})();
</script>
@endpush
@endif
@endsection
