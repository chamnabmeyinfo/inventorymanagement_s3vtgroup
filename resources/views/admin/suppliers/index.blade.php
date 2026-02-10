@extends('layouts.admin')

@section('title', 'Suppliers')

@section('content')
<div class="card" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
    <h2 style="margin: 0; flex: 1 1 100%;">Suppliers</h2>
    <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Name, contact, email">
        </div>
        <button type="submit" class="btn btn-secondary">Search</button>
    </form>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary">Add supplier</a>
</div>
<div class="card">
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
                    <td>
                        <a href="{{ route('admin.suppliers.edit', $s) }}" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.875rem;">Edit</a>
                        <form action="{{ route('admin.suppliers.destroy', $s) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this supplier?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.6rem; font-size: 0.875rem;">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No suppliers. Add one to track where your stock comes from.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $suppliers->withQueryString()->links() }}
</div>
@endsection
