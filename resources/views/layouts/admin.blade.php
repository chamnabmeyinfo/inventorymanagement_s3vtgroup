<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - S3VT Inventory</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: system-ui, sans-serif; background: #f1f5f9; color: #1e293b; }
        a { color: #2563eb; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .header { background: #0f172a; color: #f8fafc; padding: 0.75rem 1rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; position: sticky; top: 0; z-index: 100; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .header h1 { margin: 0; font-size: 1.25rem; }
        .header nav { display: flex; gap: 1rem; align-items: center; }
        .header nav a { color: #94a3b8; }
        .header nav a:hover { color: #f8fafc; }
        .container { max-width: 1200px; margin: 0 auto; padding: 1rem; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); padding: 1.25rem; margin-bottom: 1rem; }
        .btn { display: inline-flex; align-items: center; padding: 0.5rem 1rem; border-radius: 6px; border: none; font-weight: 500; cursor: pointer; font: inherit; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #e2e8f0; color: #475569; }
        .btn-secondary:hover { background: #cbd5e1; }
        .btn-danger { background: #dc2626; color: #fff; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.25rem; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.6rem 0.75rem; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { font-weight: 600; background: #f8fafc; }
        tbody tr { transition: background 0.1s; }
        tbody tr:hover { background: #f8fafc; }
        .alert { padding: 0.75rem 1rem; border-radius: 6px; margin-bottom: 1rem; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .badge { display: inline-block; padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.875rem; }
        .badge-in_stock { background: #dcfce7; color: #166534; }
        .badge-on_order { background: #fef3c7; color: #92400e; }
        .badge-out_of_stock { background: #fee2e2; color: #991b1b; }
        .pagination { display: flex; gap: 0.5rem; margin-top: 1rem; list-style: none; padding: 0; }
        .pagination li { display: inline-flex; }
        .pagination a, .pagination span { padding: 0.35rem 0.75rem; border-radius: 4px; font-size: 0.875rem; }
        .pagination .active { background: #2563eb; color: #fff; }
        .pagination li.disabled span { color: #94a3b8; cursor: not-allowed; }
    </style>
    @stack('styles')
</head>
<body>
    <header class="header">
        <h1>S3VT Inventory Admin</h1>
        <nav>
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.products.index') }}">Products</a>
            <a href="{{ route('admin.categories.index') }}">Categories</a>
            <a href="{{ route('admin.suppliers.index') }}">Suppliers</a>
            <a href="{{ route('admin.stock-movements.create') }}">Stock movement</a>
            <a href="{{ route('admin.reports.index') }}">Reports</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.settings.index') }}">Settings</a>
                <a href="{{ route('admin.users.index') }}">Users</a>
            @endif
            <span style="color: #64748b; margin-right: 0.5rem;">{{ auth()->user()->name }}</span>
            <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary">Logout</button>
            </form>
        </nav>
    </header>
    <main class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                <ul style="margin: 0; padding-left: 1.25rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
