@extends('layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="card" style="display: flex; justify-content: space-between; align-items: center;">
    <h2 style="margin: 0;">Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Add category</a>
</div>
<div class="card">
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
                    <td>
                        <a href="{{ route('admin.categories.edit', $c) }}" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.875rem;">Edit</a>
                        <form action="{{ route('admin.categories.destroy', $c) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this category?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.6rem; font-size: 0.875rem;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No categories. Add one to get started.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $categories->links() }}
</div>
@endsection
