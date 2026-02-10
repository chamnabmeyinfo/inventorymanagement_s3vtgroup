@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="card" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
    <h2 style="margin: 0; flex: 1 1 100%;">Users</h2>
    <form method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label>Search</label>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Name or email">
        </div>
        <button type="submit" class="btn btn-secondary">Search</button>
    </form>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add user</a>
</div>
<div class="card">
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
                    <td><span class="badge" style="background: #e2e8f0; color: #475569;">{{ $u->role }}</span></td>
                    <td>
                        <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.875rem;">Edit</a>
                        @if($u->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.6rem; font-size: 0.875rem;">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No users.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $users->withQueryString()->links() }}
</div>
@endsection
