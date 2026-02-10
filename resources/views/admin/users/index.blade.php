@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="card">
    <div class="page-header">
        <h2>Users</h2>
        <div class="btn-group">
            <form method="GET" class="filter-form">
                <div class="form-group">
                    <label>Search</label>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="Name or email">
                </div>
                <button type="submit" class="btn btn-secondary">Search</button>
            </form>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add user</a>
        </div>
    </div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
                <tr>
                    <td>{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge badge-neutral">{{ $u->role }}</span></td>
                    <td class="action-cell">
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-secondary btn-sm">Edit</a>
                        @if($u->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No users.</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    {{ $users->withQueryString()->links() }}
</div>
@endsection
