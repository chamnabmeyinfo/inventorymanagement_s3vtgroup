<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - S3VT Inventory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-bg: #f1f5f9;
            --color-surface: #ffffff;
            --color-primary: #0f766e;
            --color-primary-hover: #0d9488;
            --color-primary-light: #ccfbf1;
            --color-text: #1e293b;
            --color-text-muted: #64748b;
            --color-border: #e2e8f0;
            --color-danger: #dc2626;
            --color-danger-bg: #fef2f2;
            --color-success: #166534;
            --color-success-bg: #dcfce7;
            --color-warning: #92400e;
            --color-warning-bg: #fef3c7;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
            --radius: 8px;
            --radius-lg: 12px;
            --header-height: 56px;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Inter', system-ui, -apple-system, sans-serif; background: var(--color-bg); color: var(--color-text); font-size: 15px; line-height: 1.5; }
        a { color: var(--color-primary); text-decoration: none; transition: color 0.15s; }
        a:hover { color: var(--color-primary-hover); }

        .header {
            background: #0f172a;
            color: #f8fafc;
            height: var(--header-height);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 1rem;
            position: sticky;
            top: 0;
            z-index: 200;
            box-shadow: var(--shadow-md);
        }
        .header-brand { font-weight: 700; font-size: 1.125rem; color: #fff; }
        .header-brand:hover { color: #f8fafc; }
        .nav-toggle { display: none; background: none; border: none; color: #f8fafc; padding: 0.5rem; cursor: pointer; font-size: 1.5rem; }
        .nav-toggle:focus { outline: none; }
        .header-nav { display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
        .header-nav a { color: #94a3b8; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.9375rem; }
        .header-nav a:hover { color: #f8fafc; background: rgba(255,255,255,0.08); }
        .header-user { color: #94a3b8; font-size: 0.875rem; margin-right: 0.5rem; }
        .header .btn { padding: 0.375rem 0.75rem; font-size: 0.875rem; }

        @media (max-width: 768px) {
            .nav-toggle { display: block; }
            .header-nav { display: none; position: absolute; top: var(--header-height); left: 0; right: 0; background: #0f172a; flex-direction: column; align-items: stretch; padding: 0.5rem; gap: 0.25rem; box-shadow: var(--shadow-md); }
            .header-nav.open { display: flex; }
            .header-nav a { padding: 0.75rem 1rem; }
        }

        .container { max-width: 1280px; margin: 0 auto; padding: 1rem; }
        @media (max-width: 640px) { .container { padding: 0.75rem; } }

        .card {
            background: var(--color-surface);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 1.25rem;
            margin-bottom: 1rem;
        }
        .card h2, .card h3 { margin: 0 0 0.5rem 0; font-size: 1.25rem; font-weight: 600; }
        .card h3 { font-size: 1.0625rem; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.5rem 1rem; border-radius: 6px; border: none;
            font-weight: 500; font-size: 0.9375rem; cursor: pointer; font-family: inherit;
            transition: background 0.15s, transform 0.05s;
        }
        .btn:active { transform: scale(0.98); }
        .btn-primary { background: var(--color-primary); color: #fff; }
        .btn-primary:hover { background: var(--color-primary-hover); }
        .btn-secondary { background: #e2e8f0; color: #475569; }
        .btn-secondary:hover { background: #cbd5e1; }
        .btn-danger { background: var(--color-danger); color: #fff; }
        .btn-danger:hover { background: #b91c1c; }

        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.375rem; font-weight: 500; font-size: 0.9375rem; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--color-border);
            border-radius: 6px; font-size: 0.9375rem; font-family: inherit;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none; border-color: var(--color-primary); box-shadow: 0 0 0 3px var(--color-primary-light);
        }

        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; margin: 0 -0.5rem; }
        table { width: 100%; min-width: 500px; border-collapse: collapse; font-size: 0.9375rem; }
        th, td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--color-border); }
        th { font-weight: 600; background: #f8fafc; color: var(--color-text); }
        tbody tr { transition: background 0.1s; }
        tbody tr:hover { background: #f8fafc; }

        .alert { padding: 0.875rem 1rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9375rem; }
        .alert-success { background: var(--color-success-bg); color: var(--color-success); }
        .alert-error { background: var(--color-danger-bg); color: var(--color-danger); }

        .badge { display: inline-block; padding: 0.2rem 0.55rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 500; }
        .badge-in_stock { background: var(--color-success-bg); color: var(--color-success); }
        .badge-on_order { background: var(--color-warning-bg); color: var(--color-warning); }
        .badge-out_of_stock { background: var(--color-danger-bg); color: var(--color-danger); }
        .badge-neutral { background: #e2e8f0; color: #475569; }

        .pagination { display: flex; flex-wrap: wrap; gap: 0.5rem; margin-top: 1rem; list-style: none; padding: 0; }
        .pagination li { display: inline-flex; }
        .pagination a, .pagination span {
            padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.875rem;
            background: #f1f5f9; color: var(--color-text);
        }
        .pagination a:hover { background: #e2e8f0; }
        .pagination .active span { background: var(--color-primary); color: #fff; }
        .pagination li.disabled span { color: var(--color-text-muted); cursor: not-allowed; }

        .page-header { display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; justify-content: space-between; margin-bottom: 1rem; }
        .page-header h2 { margin: 0; font-size: 1.5rem; font-weight: 700; }
        .btn-group { display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center; }
        .btn-sm { padding: 0.35rem 0.65rem; font-size: 0.8125rem; }
        .action-cell { white-space: nowrap; }
        .action-cell .btn, .action-cell form { display: inline-flex; align-items: center; }
        .action-cell form { margin: 0; }
        .filter-form { display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end; }
        .filter-form .form-group { margin-bottom: 0; }
        .filter-form .form-group input, .filter-form .form-group select { min-width: 140px; }
        .form-group small { display: block; margin-top: 0.25rem; font-size: 0.8125rem; color: var(--color-text-muted); }
        .form-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 1rem; }
    </style>
    @stack('styles')
</head>
<body>
    <header class="header">
        <a href="{{ route('admin.dashboard') }}" class="header-brand">S3VT Inventory</a>
        <button type="button" class="nav-toggle" id="navToggle" aria-label="Toggle menu">☰</button>
        <nav class="header-nav" id="headerNav">
            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            <a href="{{ route('admin.products.index') }}">Products</a>
            <a href="{{ route('admin.categories.index') }}">Categories</a>
            <a href="{{ route('admin.suppliers.index') }}">Suppliers</a>
            <a href="{{ route('admin.stock-movements.create') }}">Stock</a>
            <a href="{{ route('admin.reports.index') }}">Reports</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.settings.index') }}">Settings</a>
                <a href="{{ route('admin.users.index') }}">Users</a>
            @endif
            <span class="header-user">{{ auth()->user()->name }}</span>
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
    <script>
        document.getElementById('navToggle')?.addEventListener('click', function() {
            document.getElementById('headerNav').classList.toggle('open');
        });
    </script>
</body>
</html>
