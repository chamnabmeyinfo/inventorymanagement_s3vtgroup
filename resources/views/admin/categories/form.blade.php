@extends('layouts.admin')

@section('title', $category ? 'Edit category' : 'New category')

@section('content')
<div class="card">
    <h2 style="margin-top: 0;">{{ $category ? 'Edit category' : 'New category' }}</h2>
    <form method="POST" action="{{ $category ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
        @csrf
        @if($category) @method('PUT') @endif
        <div class="form-group">
            <label>Name *</label>
            <input name="name" value="{{ old('name', $category?->name) }}" required>
        </div>
        <div class="form-group">
            <label>Slug</label>
            <input name="slug" value="{{ old('slug', $category?->slug) }}" placeholder="auto from name">
        </div>
        <div class="form-group">
            <label>Image URL</label>
            <input name="image_url" value="{{ old('image_url', $category?->image_url) }}" placeholder="/storage/...">
        </div>
        <div class="form-group">
            <label>Sort order</label>
            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $category?->sort_order ?? 0) }}">
        </div>
        <div style="margin-top: 1rem; display: flex; gap: 0.5rem;">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
