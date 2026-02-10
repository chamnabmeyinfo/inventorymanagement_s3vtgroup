@extends('layouts.admin')

@section('title', $user ? 'Edit user' : 'New user')

@section('content')
<div class="card">
    <h2 style="margin-top: 0;">{{ $user ? 'Edit user' : 'New user' }}</h2>
    <form method="POST" action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}">
        @csrf
        @if($user) @method('PUT') @endif
        <div class="form-group">
            <label>Name *</label>
            <input name="name" value="{{ old('name', $user?->name) }}" required placeholder="Full name">
        </div>
        <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="{{ old('email', $user?->email) }}" required placeholder="user@example.com" {{ $user ? '' : 'autocomplete=off' }}>
        </div>
        <div class="form-group">
            <label>Password {{ $user ? '(leave blank to keep current)' : '*' }}</label>
            <input type="password" name="password" {{ $user ? '' : 'required' }} minlength="8" autocomplete="new-password">
        </div>
        @if(!$user)
            <div class="form-group">
                <label>Confirm password *</label>
                <input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password">
            </div>
        @else
            <div class="form-group">
                <label>Confirm password</label>
                <input type="password" name="password_confirmation" autocomplete="new-password">
            </div>
        @endif
        <div class="form-group">
            <label>Role *</label>
            <select name="role" required>
                <option value="viewer" {{ old('role', $user?->role) === 'viewer' ? 'selected' : '' }}>Viewer</option>
                <option value="editor" {{ old('role', $user?->role) === 'editor' ? 'selected' : '' }}>Editor</option>
                <option value="admin" {{ old('role', $user?->role) === 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
            <small style="color: #64748b;">Viewer: read-only. Editor: can create/edit. Admin: full access including users and settings.</small>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
