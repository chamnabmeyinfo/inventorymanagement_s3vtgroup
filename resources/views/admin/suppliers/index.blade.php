@extends('layouts.admin')

@section('title', 'Suppliers')

@section('content')
<div class="card">
    <div class="page-header">
        <h2>Suppliers</h2>
        <div class="btn-group">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label>Search</label>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Name, contact, email">
                </div>
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>
            <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">Add supplier</a>
        </div>
    </div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Contact</th>
                <th>Phone</th>
                <th>Email</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $s)
                <tr>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->contact_person ?? '—' }}</td>
                    <td>{{ $s->phone ?? '—' }}</td>
                    <td>{{ $s->email ?? '—' }}</td>
                    <td class="action-cell">
                        <a href="{{ route('admin.suppliers.edit', $s) }}" class="btn btn-secondary btn-sm">Edit</a>
                        <form action="{{ route('admin.suppliers.duplicate', $s) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-secondary btn-sm">Duplicate</button>
                        </form>
                        <form action="{{ route('admin.suppliers.destroy', $s) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this supplier?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No suppliers. Add one to track where your stock comes from.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    {{ $suppliers->withQueryString()->links() }}
</div>
@endsection
