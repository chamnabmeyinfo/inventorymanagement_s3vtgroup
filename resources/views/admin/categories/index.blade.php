@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="card">
    <div class="page-header">
        <h2>Categories</h2>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Add category</a>
    </div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Sort order</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $c)
                <tr>
                    <td>{{ $c->name }}</td>
                    <td style="color: #64748b;">{{ $c->slug }}</td>
                    <td>{{ $c->sort_order }}</td>
                    <td class="action-cell">
                        <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <form action="{{ route('admin.categories.duplicate', $c) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Duplicate</button>
                        </form>
                        <form action="{{ route('admin.categories.destroy', $c) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No categories. Add one to get started.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    {{ $categories->links() }}
</div>
@endsection
