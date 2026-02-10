<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - S3VT Inventory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --win-bg: #E8ECF1;
            --win-bg-end: #DDE4EB;
            --win-surface: rgba(255, 255, 255, 0.65);
            --win-surface-solid: #FFFFFF;
            --win-surface-hover: rgba(255, 255, 255, 0.5);
            --win-glass-border: rgba(255, 255, 255, 0.5);
            --win-glass-border-strong: rgba(255, 255, 255, 0.7);
            --win-accent: #0078D4;
            --win-accent-hover: #106EBE;
            --win-accent-subtle: rgba(0, 120, 212, 0.12);
            --win-text: #1F1F1F;
            --win-text-secondary: #605E5C;
            --win-border: rgba(0, 0, 0, 0.06);
            --win-border-strong: rgba(0, 0, 0, 0.1);
            --win-blur: 20px;
            --win-blur-strong: 32px;
            --win-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
            --win-shadow-sm: 0 4px 16px rgba(0, 0, 0, 0.06);
            --win-radius: 8px;
            --win-radius-lg: 14px;
            --win-radius-xl: 18px;
            --win-danger: #D13438;
            --win-success: #107C10;
            --win-warning: #F7630C;
            --sidebar-width: 52px;
            --sidebar-width-expanded: 220px;
            --topbar-height: 48px;
            /* Ubuntu sidebar – light */
            --sidebar-bg: #E8E8E8;
            --sidebar-border: rgba(0, 0, 0, 0.08);
            --sidebar-text: #2D2D2D;
            --sidebar-text-muted: #5E5E5E;
            --sidebar-hover-bg: rgba(0, 0, 0, 0.06);
            --sidebar-active-bg: rgba(233, 84, 32, 0.15);
            --sidebar-active-fg: #E95420;
            --sidebar-accent: #E95420;
            --sidebar-accent-hover: #C34113;
        }
        [data-theme="dark"] {
            --win-bg: #1C1C1C;
            --win-bg-end: #252525;
            --win-surface: rgba(45, 45, 45, 0.85);
            --win-glass-border: rgba(255, 255, 255, 0.08);
            --win-glass-border-strong: rgba(255, 255, 255, 0.12);
            --win-text: #E8E8E8;
            --win-text-secondary: #A0A0A0;
            --win-border: rgba(255, 255, 255, 0.08);
            --win-border-strong: rgba(255, 255, 255, 0.12);
            --win-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            --win-shadow-sm: 0 4px 16px rgba(0, 0, 0, 0.3);
            --win-accent: #E95420;
            --win-accent-hover: #FF6B35;
            --win-accent-subtle: rgba(233, 84, 32, 0.2);
            /* Ubuntu sidebar – dark */
            --sidebar-bg: #2D2D2D;
            --sidebar-border: rgba(255, 255, 255, 0.06);
            --sidebar-text: #E8E8E8;
            --sidebar-text-muted: #A0A0A0;
            --sidebar-hover-bg: rgba(255, 255, 255, 0.08);
            --sidebar-active-bg: rgba(233, 84, 32, 0.25);
            --sidebar-active-fg: #FF6B35;
            --sidebar-accent: #E95420;
            --sidebar-accent-hover: #FF6B35;
        }
        [data-theme="dark"] body {
            background: linear-gradient(160deg, var(--win-bg) 0%, var(--win-bg-end) 50%, #2A2A2A 100%);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(160deg, var(--win-bg) 0%, var(--win-bg-end) 50%, #D4DCE4 100%);
            background-attachment: fixed;
            color: var(--win-text); font-size: 14px; line-height: 1.5;
            min-height: 100vh;
            transition: background 0.25s ease, color 0.2s ease;
        }
        a { color: var(--win-accent); text-decoration: none; transition: color 0.2s ease; }
        a:hover { color: var(--win-accent-hover); }

        .app-shell { display: flex; min-height: 100vh; --sidebar-actual-width: var(--sidebar-width); }
        .app-shell.sidebar-expanded { --sidebar-actual-width: var(--sidebar-width-expanded); }
        /* Ubuntu-style sidebar – expand/collapse */
        .sidebar {
            width: var(--sidebar-actual-width); flex-shrink: 0;
            font-family: 'Ubuntu', system-ui, sans-serif;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex; flex-direction: column; align-items: center;
            padding: 12px 0; gap: 4px;
            transition: width 0.2s ease, background 0.25s ease, border-color 0.25s ease;
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        .app-shell.sidebar-expanded .sidebar { align-items: stretch; padding-left: 6px; padding-right: 6px; }
        [data-theme="dark"] .sidebar { box-shadow: 2px 0 16px rgba(0, 0, 0, 0.3); }
        .sidebar-brand {
            width: 40px; height: 40px; min-width: 40px; display: flex; align-items: center; justify-content: center;
            border-radius: 10px; background: var(--sidebar-accent); color: #fff;
            font-weight: 700; font-size: 0.75rem; margin-bottom: 8px;
            box-shadow: 0 2px 8px rgba(233, 84, 32, 0.4);
            transition: background 0.2s ease, transform 0.15s ease;
        }
        .app-shell.sidebar-expanded .sidebar-brand { width: 100%; min-width: 0; }
        .sidebar-brand:hover { background: var(--sidebar-accent-hover); color: #fff; transform: scale(1.02); }
        .sidebar-nav { display: flex; flex-direction: column; gap: 4px; flex: 1; width: 100%; padding: 0 2px; }
        .sidebar-nav a {
            width: 40px; height: 40px; min-width: 40px; display: flex; align-items: center; justify-content: center; gap: 8px;
            border-radius: 10px; color: var(--sidebar-text-muted);
            transition: background 0.2s ease, color 0.2s ease; flex-shrink: 0;
        }
        .app-shell.sidebar-expanded .sidebar-nav a { width: 100%; min-width: 0; justify-content: flex-start; padding: 0 12px; }
        .sidebar-nav a:hover { background: var(--sidebar-hover-bg); color: var(--sidebar-accent); }
        .sidebar-nav a.active { background: var(--sidebar-active-bg); color: var(--sidebar-active-fg); }
        .sidebar-nav svg { width: 20px; height: 20px; flex-shrink: 0; }
        .sidebar-label { display: none; font-size: 0.875rem; white-space: nowrap; overflow: hidden; }
        .app-shell.sidebar-expanded .sidebar-label { display: block; }
        .sidebar-toggle {
            width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;
            border-radius: 10px; color: var(--sidebar-text-muted);
            background: transparent; border: none; cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease; margin-top: 4px; flex-shrink: 0;
        }
        .sidebar-toggle:hover { background: var(--sidebar-hover-bg); color: var(--sidebar-accent); }
        .sidebar-toggle svg { width: 20px; height: 20px; }
        .sidebar-toggle .icon-expand { display: none; }
        .sidebar-toggle .icon-collapse { display: block; }
        .app-shell:not(.sidebar-expanded) .sidebar-toggle .icon-expand { display: block; }
        .app-shell:not(.sidebar-expanded) .sidebar-toggle .icon-collapse { display: none; }
        .sidebar-theme .icon-dark { display: none; }
        .sidebar-theme .icon-light { display: block; }
        [data-theme="dark"] .sidebar-theme .icon-dark { display: block; }
        [data-theme="dark"] .sidebar-theme .icon-light { display: none; }

        .main-area { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .topbar {
            position: sticky; top: 0; z-index: 100;
            height: var(--topbar-height); flex-shrink: 0;
            background: rgba(255, 255, 255, 0.4);
            -webkit-backdrop-filter: blur(var(--win-blur-strong)) saturate(180%);
            backdrop-filter: blur(var(--win-blur-strong)) saturate(180%);
            border-bottom: 1px solid var(--win-glass-border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 20px;
        }
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        .start-btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 8px 14px; border-radius: 10px; border: 1px solid transparent;
            background: var(--sidebar-bg); color: var(--sidebar-text);
            font-family: 'Ubuntu', system-ui, sans-serif; font-weight: 500; font-size: 0.875rem;
            cursor: pointer; transition: background 0.2s ease, border-color 0.2s ease, color 0.2s ease;
        }
        .start-btn:hover { background: var(--sidebar-hover-bg); color: var(--sidebar-accent); }
        .start-btn svg { width: 20px; height: 20px; flex-shrink: 0; }
        .topbar-title { font-size: 0.875rem; font-weight: 600; color: var(--win-text); }
        .topbar-user { display: flex; align-items: center; gap: 10px; font-size: 0.8125rem; color: var(--win-text-secondary); }
        .topbar-user .btn { padding: 6px 14px; font-size: 0.8125rem; }
        .topbar .sidebar-theme {
            display: inline-flex; align-items: center; justify-content: center;
            width: 36px; height: 36px; min-width: 36px;
            border-radius: 10px; color: var(--win-text-secondary);
            background: transparent; border: none; cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease;
        }
        .topbar .sidebar-theme:hover { background: rgba(0, 0, 0, 0.06); color: var(--win-accent); }
        [data-theme="dark"] .topbar .sidebar-theme:hover { background: rgba(255, 255, 255, 0.08); color: var(--win-accent); }
        .topbar .sidebar-theme svg { width: 18px; height: 18px; }

        /* Start menu popup */
        .start-menu-overlay { display: none; position: fixed; inset: 0; z-index: 200; }
        .start-menu-overlay.open { display: block; }
        .start-menu {
            position: fixed; left: calc(var(--sidebar-actual-width) + 12px); top: calc(var(--topbar-height) + 8px);
            width: min(420px, calc(100vw - var(--sidebar-actual-width) - 40px));
            max-height: min(480px, calc(100vh - var(--topbar-height) - 32px));
            background: var(--win-surface); -webkit-backdrop-filter: blur(var(--win-blur-strong)); backdrop-filter: blur(var(--win-blur-strong));
            border-radius: 14px; border: 1px solid var(--win-glass-border);
            box-shadow: var(--win-shadow); padding: 12px; z-index: 201;
            overflow-y: auto; opacity: 0; transform: scale(0.96); transform-origin: top left;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .start-menu.open { opacity: 1; transform: scale(1); }
        [data-theme="dark"] .start-menu { background: rgba(45, 45, 45, 0.95); border-color: var(--win-glass-border); }
        .start-menu-header { font-family: 'Ubuntu', system-ui, sans-serif; font-size: 0.75rem; font-weight: 600; color: var(--win-text-secondary); text-transform: uppercase; letter-spacing: 0.05em; padding: 8px 10px 6px; margin-bottom: 4px; }
        .start-menu-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; }
        .start-menu-tile {
            display: flex; align-items: center; gap: 12px; padding: 12px 14px;
            border-radius: 12px; text-decoration: none; color: var(--win-text);
            background: transparent; border: none; cursor: pointer; font-family: inherit; font-size: 0.9375rem; font-weight: 500;
            transition: background 0.2s ease, color 0.2s ease; text-align: left; width: 100%;
        }
        .start-menu-tile:hover { background: var(--win-accent-subtle); color: var(--win-accent); }
        .start-menu-tile.active { background: var(--win-accent-subtle); color: var(--win-accent); }
        .start-menu-tile svg { width: 24px; height: 24px; flex-shrink: 0; }
        @media (max-width: 768px) {
            .start-menu { left: 12px; top: 60px; width: min(360px, calc(100vw - 24px)); }
        }

        .content { flex: 1; padding: 24px; overflow-x: hidden; max-width: 1400px; margin: 0 auto; width: 100%; }
        @media (max-width: 768px) {
            .sidebar { position: fixed; left: 0; top: 0; bottom: 0; z-index: 300; transform: translateX(-100%); transition: transform 0.2s ease, width 0.2s ease; }
            .sidebar.open { transform: translateX(0); }
            .app-shell.sidebar-expanded .sidebar { width: var(--sidebar-width-expanded); }
            .main-area { margin-left: 0; }
            .content { padding: 16px; }
        }
        .nav-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.25); -webkit-backdrop-filter: blur(4px); backdrop-filter: blur(4px); z-index: 250; }
        .nav-overlay.open { display: block; }
        .nav-toggle { display: none; position: fixed; left: 12px; top: 58px; z-index: 350; width: 40px; height: 40px; border-radius: 12px; border: 1px solid var(--win-glass-border); background: rgba(255,255,255,0.5); -webkit-backdrop-filter: blur(12px); backdrop-filter: blur(12px); cursor: pointer; align-items: center; justify-content: center; box-shadow: var(--win-shadow-sm); }
        @media (max-width: 768px) { .nav-toggle { display: flex; } .topbar { padding-left: 60px; } .content { padding-top: 60px; } }

        .card {
            background: var(--win-surface);
            -webkit-backdrop-filter: blur(var(--win-blur));
            backdrop-filter: blur(var(--win-blur));
            border-radius: var(--win-radius-lg);
            box-shadow: var(--win-shadow-sm);
            border: 1px solid var(--win-glass-border);
            padding: 20px;
            margin-bottom: 16px;
        }
        .card h2, .card h3 { margin: 0 0 8px 0; font-size: 1.125rem; font-weight: 600; }
        .card h3 { font-size: 1rem; }

        .btn {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 8px 16px; border-radius: var(--win-radius);
            border: none; font-weight: 500; font-size: 0.875rem;
            cursor: pointer; font-family: inherit;
            transition: background 0.2s ease, opacity 0.2s ease;
        }
        .btn:active { opacity: 0.9; }
        .btn-primary { background: var(--win-accent); color: #fff; }
        .btn-primary:hover { background: var(--win-accent-hover); }
        .btn-secondary { background: rgba(255, 255, 255, 0.5); color: var(--win-text); border: 1px solid var(--win-glass-border); -webkit-backdrop-filter: blur(8px); backdrop-filter: blur(8px); }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.7); }
        .btn-danger { background: var(--win-danger); color: #fff; }
        .btn-danger:hover { opacity: 0.9; }

        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 0.875rem; }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%; padding: 8px 12px; border: 1px solid var(--win-glass-border);
            border-radius: var(--win-radius); font-size: 0.875rem; font-family: inherit;
            background: rgba(255, 255, 255, 0.6);
            -webkit-backdrop-filter: blur(6px);
            backdrop-filter: blur(6px);
            transition: border-color 0.2s ease, background 0.2s ease;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none; border-color: var(--win-accent); box-shadow: 0 0 0 2px var(--win-accent-subtle);
        }
        [data-theme="dark"] .form-group input, [data-theme="dark"] .form-group select, [data-theme="dark"] .form-group textarea {
            background: rgba(45, 45, 45, 0.8); border-color: rgba(255, 255, 255, 0.1); color: var(--win-text);
        }
        [data-theme="dark"] .topbar { background: rgba(45, 45, 45, 0.9); border-color: var(--win-glass-border); }
        [data-theme="dark"] .nav-toggle { background: rgba(45, 45, 45, 0.9); border-color: var(--win-glass-border); }

        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; margin: 0 -4px; }
        table { width: 100%; min-width: 500px; border-collapse: collapse; font-size: 0.875rem; }
        th, td { padding: 12px 16px; text-align: left; border-bottom: 1px solid var(--win-border); }
        th { font-weight: 600; background: rgba(255, 255, 255, 0.4); color: var(--win-text); }
        tbody tr { transition: background 0.15s ease; }
        tbody tr:hover { background: rgba(255, 255, 255, 0.35); }

        .alert { padding: 12px 16px; border-radius: var(--win-radius); margin-bottom: 16px; font-size: 0.875rem; }
        .alert-success { background: rgba(16, 124, 16, 0.1); color: var(--win-success); border: 1px solid rgba(16, 124, 16, 0.2); }
        .alert-error { background: rgba(209, 52, 56, 0.08); color: var(--win-danger); border: 1px solid rgba(209, 52, 56, 0.2); }

        .badge { display: inline-block; padding: 4px 10px; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
        .badge-in_stock { background: rgba(16, 124, 16, 0.12); color: var(--win-success); }
        .badge-on_order { background: rgba(247, 99, 12, 0.12); color: var(--win-warning); }
        .badge-out_of_stock { background: rgba(209, 52, 56, 0.12); color: var(--win-danger); }
        .badge-neutral { background: rgba(255, 255, 255, 0.5); color: var(--win-text-secondary); }

        .pagination { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 16px; list-style: none; padding: 0; }
        .pagination li { display: inline-flex; }
        .pagination a, .pagination span {
            padding: 6px 12px; border-radius: var(--win-radius); font-size: 0.8125rem;
            background: rgba(255, 255, 255, 0.5); color: var(--win-text); border: 1px solid var(--win-glass-border);
        }
        .pagination a:hover { background: rgba(255, 255, 255, 0.7); }
        .pagination .active span { background: var(--win-accent); color: #fff; border-color: var(--win-accent); }
        .pagination li.disabled span { color: var(--win-text-secondary); cursor: not-allowed; }

        .page-header { display: flex; flex-wrap: wrap; gap: 16px; align-items: flex-end; justify-content: space-between; margin-bottom: 20px; }
        .page-header h2 { margin: 0; font-size: 1.5rem; font-weight: 600; }
        .btn-group { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
        .btn-sm { padding: 6px 12px; font-size: 0.8125rem; }
        .action-cell { white-space: nowrap; }
        .action-cell .btn, .action-cell form { display: inline-flex; align-items: center; }
        .action-cell form { margin: 0; }
        .filter-form { display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end; }
        .filter-form .form-group { margin-bottom: 0; }
        .filter-form .form-group input, .filter-form .form-group select { min-width: 140px; }
        .form-group small { display: block; margin-top: 4px; font-size: 0.8125rem; color: var(--win-text-secondary); }
        .form-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 16px; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar" id="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand" title="S3VT Inventory">S3</a>
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 13h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1zm-1 7v-4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1zm10-8h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1zm1 7v-4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-6a1 1 0 0 1-1-1z"/></svg><span class="sidebar-label">Dashboard</span></a>
                <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" title="Products"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-4V4c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM10 4h4v2h-4V4zm10 16H4V8h16v12z"/></svg><span class="sidebar-label">Products</span></a>
                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" title="Categories"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 8h4V4H4v4zm6 12h4v-4h-4v4zm-6 0h4v-4H4v4zm0-6h4v-4H4v4zm6 0h4v-4h-4v4zm6-10v4h4V4h-4zm-6 4h4V4h-4v4zm6 6h4v-4h-4v4zm0 6h4v-4h-4v4z"/></svg><span class="sidebar-label">Categories</span></a>
                <a href="{{ route('admin.suppliers.index') }}" class="{{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}" title="Suppliers"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg><span class="sidebar-label">Suppliers</span></a>
                <a href="{{ route('admin.stock-movements.create') }}" class="{{ request()->routeIs('admin.stock-movements.*') ? 'active' : '' }}" title="Stock"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2z"/></svg><span class="sidebar-label">Stock</span></a>
                <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" title="Reports"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg><span class="sidebar-label">Reports</span></a>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" title="Settings"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.14 12.94c.04-.31.06-.63.06-.94 0-.31-.02-.63-.06-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.04.31-.06.63-.06.94s.02.63.06.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg><span class="sidebar-label">Settings</span></a>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" title="Users"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.04-.96.07 1.16.84 1.96 1.96 1.96 3.43V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg><span class="sidebar-label">Users</span></a>
                @endif
            </nav>
            <button type="button" class="sidebar-toggle" id="sidebarToggle" aria-label="Expand sidebar" title="Expand / collapse sidebar">
                <span class="icon-expand"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"/></svg></span>
                <span class="icon-collapse"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/></svg></span>
            </button>
        </aside>
        <div class="main-area">
            <header class="topbar">
                <div class="topbar-left">
                    <button type="button" class="start-btn" id="startBtn" aria-label="Open menu" title="Start">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h8v8H3V3zm10 0h8v8h-8V3zM3 13h8v8H3v-8zm10 0h8v8h-8v-8z"/></svg>
                        <span>Start</span>
                    </button>
                    <span class="topbar-title">@yield('title', 'Admin')</span>
                </div>
                <div class="topbar-user">
                    <span>{{ auth()->user()->name }}</span>
                    <button type="button" class="sidebar-theme" id="themeToggle" aria-label="Toggle light/dark mode" title="Toggle theme">
                        <span class="icon-light"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9c0-.46-.04-.92-.1-1.36-.98 1.37-2.58 2.26-4.4 2.26-2.98 0-5.4-2.42-5.4-5.4 0-1.81.89-3.42 2.26-4.4-.44-.06-.9-.1-1.36-.1z"/></svg></span>
                        <span class="icon-dark"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 0 0-1.41 0 .996.996 0 0 0 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 0 0-1.41 0 .996.996 0 0 0 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0a.996.996 0 0 0 0-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 0 0 0-1.41.996.996 0 0 0-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.36a.996.996 0 0 0 0-1.41.996.996 0 0 0-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z"/></svg></span>
                    </button>
                    <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Sign out</button>
                    </form>
                </div>
            </header>
            <main class="content">
                <div class="start-menu-overlay" id="startMenuOverlay"></div>
                <div class="start-menu" id="startMenu" role="menu" aria-label="Modules">
                    <div class="start-menu-header">Modules</div>
                    <div class="start-menu-grid">
                        <a href="{{ route('admin.dashboard') }}" class="start-menu-tile {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" role="menuitem">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 13h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1zm-1 7v-4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1zm10-8h6a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1h-6a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1zm1 7v-4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1h-6a1 1 0 0 1-1-1z"/></svg>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="start-menu-tile {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" role="menuitem">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 6h-4V4c0-1.1-.9-2-2-2h-4c-1.1 0-2 .9-2 2v2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zM10 4h4v2h-4V4zm10 16H4V8h16v12z"/></svg>
                            <span>Products</span>
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="start-menu-tile {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" role="menuitem">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M4 8h4V4H4v4zm6 12h4v-4h-4v4zm-6 0h4v-4H4v4zm0-6h4v-4H4v4zm6 0h4v-4h-4v4zm6-10v4h4V4h-4zm-6 4h4V4h-4v4zm6 6h4v-4h-4v4zm0 6h4v-4h-4v4z"/></svg>
                            <span>Categories</span>
                        </a>
                        <a href="{{ route('admin.suppliers.index') }}" class="start-menu-tile {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}" role="menuitem">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
                            <span>Suppliers</span>
                        </a>
                        <a href="{{ route('admin.stock-movements.create') }}" class="start-menu-tile {{ request()->routeIs('admin.stock-movements.*') ? 'active' : '' }}" role="menuitem">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-2 10H7v-2h10v2z"/></svg>
                            <span>Stock</span>
                        </a>
                        <a href="{{ route('admin.reports.index') }}" class="start-menu-tile {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" role="menuitem">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/></svg>
                            <span>Reports</span>
                        </a>
                        @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.settings.index') }}" class="start-menu-tile {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" role="menuitem">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.14 12.94c.04-.31.06-.63.06-.94 0-.31-.02-.63-.06-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94l-.36-2.54c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.04.31-.06.63-.06.94s.02.63.06.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z"/></svg>
                            <span>Settings</span>
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="start-menu-tile {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" role="menuitem">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.04-.96.07 1.16.84 1.96 1.96 1.96 3.43V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg>
                            <span>Users</span>
                        </a>
                        @endif
                    </div>
                </div>
                <button type="button" class="nav-toggle" id="navToggle" aria-label="Open menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/></svg>
                </button>
                <div class="nav-overlay" id="navOverlay"></div>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif
                @if(isset($errors) && $errors->any())
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 1.25rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
    <script>
        (function(){
            var appShell = document.querySelector('.app-shell');
            var toggle = document.getElementById('navToggle');
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('navOverlay');
            function openNav() { sidebar.classList.add('open'); overlay.classList.add('open'); }
            function closeNav() { sidebar.classList.remove('open'); overlay.classList.remove('open'); }
            toggle && toggle.addEventListener('click', openNav);
            overlay && overlay.addEventListener('click', closeNav);

            var sidebarExpandedKey = 's3vt-sidebar-expanded';
            function getSidebarStored() { try { return localStorage.getItem(sidebarExpandedKey); } catch (e) { return null; } }
            function setSidebarStored(v) { try { localStorage.setItem(sidebarExpandedKey, v ? '1' : '0'); } catch (e) {} }
            function setSidebarExpanded(expanded) {
                if (expanded) appShell && appShell.classList.add('sidebar-expanded');
                else appShell && appShell.classList.remove('sidebar-expanded');
                setSidebarStored(expanded);
            }
            if (getSidebarStored() === '1') setSidebarExpanded(true);
            var sidebarToggleBtn = document.getElementById('sidebarToggle');
            if (sidebarToggleBtn) sidebarToggleBtn.addEventListener('click', function() {
                var expanded = appShell && appShell.classList.contains('sidebar-expanded');
                setSidebarExpanded(!expanded);
            });

            var themeKey = 's3vt-theme';
            var themeToggle = document.getElementById('themeToggle');
            function getStored() { try { return localStorage.getItem(themeKey); } catch (e) { return null; } }
            function setStored(v) { try { if (v) localStorage.setItem(themeKey, v); else localStorage.removeItem(themeKey); } catch (e) {} }
            function applyTheme(dark) {
                document.documentElement.setAttribute('data-theme', dark ? 'dark' : '');
                setStored(dark ? 'dark' : 'light');
            }
            var stored = getStored();
            if (stored === 'dark') applyTheme(true);
            else if (stored === 'light') applyTheme(false);
            else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) applyTheme(true);
            if (themeToggle) themeToggle.addEventListener('click', function(){
                var isDark = document.documentElement.getAttribute('data-theme') === 'dark';
                applyTheme(!isDark);
            });

            var startBtn = document.getElementById('startBtn');
            var startMenu = document.getElementById('startMenu');
            var startMenuOverlay = document.getElementById('startMenuOverlay');
            function openStartMenu() {
                setSidebarExpanded(false);
                closeNav();
                startMenu && startMenu.classList.add('open');
                startMenuOverlay && startMenuOverlay.classList.add('open');
            }
            function closeStartMenu() { startMenu && startMenu.classList.remove('open'); startMenuOverlay && startMenuOverlay.classList.remove('open'); }
            if (startBtn) startBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                if (startMenu && startMenu.classList.contains('open')) closeStartMenu();
                else openStartMenu();
            });
            if (startMenuOverlay) startMenuOverlay.addEventListener('click', closeStartMenu);
            startMenu && startMenu.querySelectorAll('.start-menu-tile').forEach(function(tile) { tile.addEventListener('click', closeStartMenu); });
        })();
    </script>
</body>
</html>
