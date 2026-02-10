@extends('layouts.admin')

@section('title', $supplier ? 'Edit supplier' : 'New supplier')

@section('content')
<div class="card">
    <h2 style="margin-top: 0;">{{ $supplier ? 'Edit supplier' : 'New supplier' }}</h2>
    <form method="POST" action="{{ $supplier ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}">
        @csrf
        @if($supplier) @method('PUT') @endif
        <div class="form-group">
            <label>Name *</label>
            <input name="name" value="{{ old('name', $supplier?->name) }}" required placeholder="Supplier or company name">
        </div>
        <div class="form-group">
            <label>Contact person</label>
            <input name="contact_person" value="{{ old('contact_person', $supplier?->contact_person) }}" placeholder="Account manager, sales rep">
        </div>
        <div class="form-group">
            <label>Phone</label>
            <input name="phone" value="{{ old('phone', $supplier?->phone) }}" placeholder="+855...">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $supplier?->email) }}" placeholder="orders@supplier.com">
        </div>
        <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="2">{{ old('address', $supplier?->address) }}</textarea>
        </div>
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" rows="2">{{ old('notes', $supplier?->notes) }}</textarea>
        </div>
        <div class="form-group">
            <label>Sort order</label>
            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $supplier?->sort_order ?? 0) }}">
        </div>
        <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
