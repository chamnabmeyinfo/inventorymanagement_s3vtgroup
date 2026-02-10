@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    .dash-welcome { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
    .dash-welcome h1 { margin: 0; font-size: 1.375rem; font-weight: 600; color: #1F1F1F; }
    .dash-welcome .sub { font-size: 0.875rem; color: #605E5C; margin-top: 4px; }
    .dash-period { display: flex; gap: 4px; background: rgba(255, 255, 255, 0.4); padding: 4px; border-radius: 12px; border: 1px solid rgba(255, 255, 255, 0.5); -webkit-backdrop-filter: blur(10px); backdrop-filter: blur(10px); }
    .dash-period a { padding: 8px 14px; border-radius: 10px; font-size: 0.875rem; font-weight: 500; color: #605E5C; transition: background 0.2s ease, color 0.2s ease; }
    .dash-period a:hover { color: #1F1F1F; background: rgba(255, 255, 255, 0.6); }
    .dash-period a.active { background: #0078D4; color: #fff; }

    .alert-banner { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding: 16px 20px; border-radius: 14px; background: rgba(209, 52, 56, 0.1); -webkit-backdrop-filter: blur(12px); backdrop-filter: blur(12px); border: 1px solid rgba(209, 52, 56, 0.2); }
    .alert-banner .icon { width: 40px; height: 40px; background: #D13438; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .alert-banner .icon svg { width: 22px; height: 22px; fill: #fff; }
    .alert-banner h3 { margin: 0 0 4px 0; font-size: 1rem; font-weight: 600; color: #a02828; }
    .alert-banner p { margin: 0; font-size: 0.875rem; color: #b91c1c; }
    .alert-banner .actions { display: flex; gap: 8px; }

    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; margin-bottom: 1.5rem; }
    .stat-card { background: rgba(255, 255, 255, 0.5); -webkit-backdrop-filter: blur(16px); backdrop-filter: blur(16px); border-radius: 14px; padding: 20px; border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: 0 4px 16px rgba(0,0,0,0.06); transition: box-shadow 0.2s ease, transform 0.2s ease; position: relative; overflow: hidden; }
    .stat-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; }
    .stat-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    .stat-card.total::before { background: #0078D4; }
    .stat-card.instock::before { background: #107C10; }
    .stat-card.outstock::before { background: #D13438; }
    .stat-card.lowstock::before { background: #F7630C; }
    .stat-card.movement::before { background: #5B5FC7; }
    .stat-card .num { font-size: 1.5rem; font-weight: 600; letter-spacing: -0.02em; line-height: 1.2; }
    .stat-card .label { font-size: 0.8125rem; color: #605E5C; margin-top: 4px; }
    .stat-card.movement .num { font-size: 1rem; }
    .stat-card.movement .in { color: #107C10; }
    .stat-card.movement .out { color: #D13438; }

    .dash-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 1.5rem; }
    .dash-card { background: rgba(255, 255, 255, 0.5); -webkit-backdrop-filter: blur(16px); backdrop-filter: blur(16px); border-radius: 14px; padding: 20px; border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
    .dash-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
    .dash-card-header h3 { margin: 0; font-size: 0.9375rem; font-weight: 600; color: #1F1F1F; }
    .dash-card-header .badge { font-size: 0.75rem; }
    .chart-wrap { position: relative; height: 200px; }
    .top-mover { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid rgba(255, 255, 255, 0.4); font-size: 0.875rem; transition: background 0.2s ease; }
    .top-mover:hover { background: rgba(255, 255, 255, 0.3); }
    .top-mover:last-child { border-bottom: none; }
    .top-mover a { font-weight: 500; }
    .top-mover .val { font-weight: 600; color: #605E5C; }
    .top-mover .val.positive { color: #107C10; }
    .top-mover .val .out { color: #D13438; }
    .empty-state { padding: 32px; text-align: center; color: #605E5C; font-size: 0.9375rem; }

    .tab-card { background: rgba(255, 255, 255, 0.5); -webkit-backdrop-filter: blur(16px); backdrop-filter: blur(16px); border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.5); box-shadow: 0 4px 16px rgba(0,0,0,0.06); overflow: hidden; }
    .tab-tabs { display: flex; gap: 0; border-bottom: 1px solid rgba(255, 255, 255, 0.4); padding: 0 16px; overflow-x: auto; background: rgba(255, 255, 255, 0.2); }
    .tab-tabs button { padding: 14px 16px; border: none; background: none; cursor: pointer; font: inherit; font-size: 0.875rem; font-weight: 500; color: #605E5C; border-bottom: 2px solid transparent; margin-bottom: -1px; white-space: nowrap; transition: color 0.2s ease; }
    .tab-tabs button:hover { color: #0078D4; }
    .tab-tabs button.active { color: #0078D4; border-bottom-color: #0078D4; }
    .tab-content { padding: 20px; }
    .search-wrap { display: flex; gap: 8px; align-items: center; margin-bottom: 16px; }
    .search-wrap input { flex: 1; min-width: 0; }
    .detail-table { font-size: 0.875rem; }
    .detail-table th { white-space: nowrap; font-weight: 600; }
    .detail-table tr:hover { background: rgba(255, 255, 255, 0.35); }
    .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    .progress-bar { height: 6px; background: rgba(255, 255, 255, 0.5); border-radius: 4px; overflow: hidden; margin-top: 4px; max-width: 100px; }
    .progress-bar span { display: block; height: 100%; border-radius: 4px; transition: width 0.3s ease; }

    .quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; }
    .quick-action { display: flex; align-items: center; gap: 12px; padding: 16px 20px; background: rgba(255, 255, 255, 0.5); -webkit-backdrop-filter: blur(12px); backdrop-filter: blur(12px); border-radius: 14px; border: 1px solid rgba(255, 255, 255, 0.5); text-decoration: none; color: #1F1F1F; font-weight: 500; font-size: 0.9375rem; transition: all 0.2s ease; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
    .quick-action:hover { border-color: rgba(255, 255, 255, 0.8); background: rgba(255, 255, 255, 0.65); color: #0078D4; }
    .quick-action .icon { width: 40px; height: 40px; background: rgba(0, 120, 212, 0.12); border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .quick-action:hover .icon { background: rgba(0, 120, 212, 0.2); }
    .quick-action .icon svg { width: 20px; height: 20px; fill: #0078D4; }

    @media (max-width: 640px) {
        .stat-grid { grid-template-columns: repeat(2, 1fr); }
        .dash-grid { grid-template-columns: 1fr; }
        .dash-welcome { flex-direction: column; align-items: flex-start; }
    }
</style>
@endpush

@section('content')
{{-- Welcome + Period --}}
<div class="card" style="margin-bottom: 1rem;">
    <div class="dash-welcome">
        <div>
            <h1>Inventory Dashboard</h1>
            <p class="sub">
                @if($lastMovementAt ?? null)
                    Last activity: {{ $lastMovementAt->diffForHumans() }}
                @else
                    Overview of your stock and movements
                @endif
            </p>
        </div>
        <div class="dash-period">
            <a href="{{ route('admin.dashboard', ['days' => 7]) }}" class="{{ ($days ?? 7) == 7 ? 'active' : '' }}">7 days</a>
            <a href="{{ route('admin.dashboard', ['days' => 14]) }}" class="{{ ($days ?? 7) == 14 ? 'active' : '' }}">14 days</a>
            <a href="{{ route('admin.dashboard', ['days' => 30]) }}" class="{{ ($days ?? 7) == 30 ? 'active' : '' }}">30 days</a>
        </div>
    </div>
</div>

{{-- Alert Banner --}}
@if($outOfStockCount > 0 || $lowStockCount > 0)
<div class="alert-banner" style="margin-bottom: 1.5rem;">
    <div style="display: flex; align-items: center; gap: 1rem;">
        <div class="icon">
            <svg viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/></svg>
        </div>
        <div>
            <h3>{{ $outOfStockCount + $lowStockCount }} products need attention</h3>
            <p>
                @if($outOfStockCount > 0){{ $outOfStockCount }} out of stock @endif
                @if($outOfStockCount > 0 && $lowStockCount > 0)· @endif
                @if($lowStockCount > 0){{ $lowStockCount }} below reorder point @endif
            </p>
        </div>
    </div>
    <div class="actions">
        <a href="{{ route('admin.stock-movements.create') }}" class="btn btn-primary">Record stock in</a>
        <a href="{{ route('admin.reports.index', ['tab' => 'out-of-stock']) }}" class="btn btn-secondary">View all</a>
    </div>
</div>
@endif

{{-- Stat Cards --}}
<div class="stat-grid">
    <div class="stat-card total">
        <div class="num" style="color: #0f172a;">{{ $totalProducts }}</div>
        <div class="label">Total products</div>
    </div>
    <div class="stat-card instock">
        <div class="num" style="color: #166534;">{{ $inStockCount }}</div>
        <div class="label">In stock</div>
    </div>
    <div class="stat-card outstock">
        <div class="num" style="color: #dc2626;">{{ $outOfStockCount }}</div>
        <div class="label">Out of stock</div>
    </div>
    <div class="stat-card lowstock">
        <div class="num" style="color: #b45309;">{{ $lowStockCount }}</div>
        <div class="label">Low stock</div>
    </div>
    <div class="stat-card movement">
        <div class="num"><span class="in">+{{ $recentInCount }}</span> <span class="out">-{{ $recentOutCount }}</span></div>
        <div class="label">Last {{ $days }} days in/out</div>
    </div>
</div>

{{-- Charts + Lists --}}
<div class="dash-grid">
    <div class="dash-card">
        <div class="dash-card-header">
            <h3>Stock status</h3>
        </div>
        <div class="chart-wrap">
            <canvas id="chartStockStatus"></canvas>
        </div>
    </div>
    <div class="dash-card">
        <div class="dash-card-header">
            <h3>Movement trend ({{ $days }} days)</h3>
        </div>
        <div class="chart-wrap">
            <canvas id="chartMovementTrend"></canvas>
        </div>
    </div>
    @if($topMovers->isNotEmpty())
    <div class="dash-card">
        <div class="dash-card-header">
            <h3>Top movers</h3>
            <span class="badge badge-neutral">{{ $topMovers->count() }}</span>
        </div>
        @foreach($topMovers->take(8) as $m)
        <div class="top-mover">
            <a href="{{ route('admin.products.edit', $m->product) }}">{{ \Illuminate\Support\Str::limit($m->product->name, 28) }}</a>
            <span class="val">+{{ $m->total_in }} / <span class="out">−{{ $m->total_out }}</span></span>
        </div>
        @endforeach
    </div>
    @endif
    @if(($stockBySupplier ?? collect())->isNotEmpty())
    <div class="dash-card">
        <div class="dash-card-header">
            <h3>Stock from suppliers</h3>
        </div>
        @foreach($stockBySupplier->take(6) as $r)
        <div class="top-mover">
            <a href="{{ route('admin.suppliers.edit', $r->supplier) }}">{{ \Illuminate\Support\Str::limit($r->supplier->name, 24) }}</a>
            <span class="val positive">+{{ $r->total }}</span>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Tabbed section --}}
<div class="tab-card">
    <div class="tab-tabs">
        <button type="button" class="tab-btn active" data-tab="products">Products needing attention</button>
        <button type="button" class="tab-btn" data-tab="activity">Recent activity</button>
        <button type="button" class="tab-btn" data-tab="categories">Category breakdown</button>
    </div>
    <div class="tab-content">
        <div id="panel-products" class="tab-panel active">
            <div class="search-wrap">
                <input type="text" id="searchProducts" placeholder="Search SKU, name..." style="padding: 0.5rem 0.75rem; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.875rem; width: 100%;">
                <span id="searchNoResults" class="no-results" style="display: none;">No matches</span>
            </div>
            @if($productsNeedingAttention->isNotEmpty())
                <div class="table-wrap">
                <table class="detail-table" id="productsTable">
                    <thead><tr><th>SKU</th><th>Name</th><th>Category</th><th>Qty</th><th>Reorder</th><th>Supplier</th><th></th></tr></thead>
                    <tbody>
                    @foreach($productsNeedingAttention as $p)
                    <tr>
                        <td><code style="font-size: 0.8125rem;">{{ $p->sku }}</code></td>
                        <td><a href="{{ route('admin.products.edit', $p) }}">{{ \Illuminate\Support\Str::limit($p->name, 24) }}</a></td>
                        <td>{{ $p->category?->name ?? '—' }}</td>
                        <td><strong>{{ $p->stock?->quantity ?? 0 }}</strong></td>
                        <td>{{ $p->reorder_point ?? '—' }}</td>
                        <td>@if($p->preferredSupplier)<a href="{{ route('admin.suppliers.edit', $p->preferredSupplier) }}">{{ \Illuminate\Support\Str::limit($p->preferredSupplier->name, 12) }}</a>@else—@endif</td>
                        <td><a href="{{ route('admin.stock-movements.create') }}?product_id={{ $p->id }}" class="btn btn-primary btn-sm">Record</a></td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
                <p style="margin: 1rem 0 0 0; font-size: 0.875rem;"><a href="{{ route('admin.reports.index', ['tab' => 'out-of-stock']) }}">View full report →</a></p>
            @else
                <div class="empty-state">All products are adequately stocked.</div>
            @endif
        </div>

        <div id="panel-activity" class="tab-panel">
            @if($recentMovements->isNotEmpty())
                <div class="table-wrap">
                <table class="detail-table">
                    <thead><tr><th>When</th><th>Product</th><th>SKU</th><th>Type</th><th>Qty</th><th>Supplier</th><th>Ref</th></tr></thead>
                    <tbody>
                    @foreach($recentMovements as $m)
                    <tr>
                        <td style="color: #64748b;">{{ $m->created_at->format('M j, H:i') }}</td>
                        <td><a href="{{ route('admin.products.edit', $m->product) }}">{{ \Illuminate\Support\Str::limit($m->product->name, 20) }}</a></td>
                        <td><code style="font-size: 0.8125rem;">{{ $m->product->sku }}</code></td>
                        <td><span class="badge badge-{{ $m->type === 'in' ? 'in_stock' : ($m->type === 'out' ? 'out_of_stock' : 'on_order') }}">{{ $m->type }}</span></td>
                        <td>{{ $m->quantity }}</td>
                        <td>@if($m->supplier)<a href="{{ route('admin.suppliers.edit', $m->supplier) }}">{{ \Illuminate\Support\Str::limit($m->supplier->name, 12) }}</a>@else—@endif</td>
                        <td>{{ \Illuminate\Support\Str::limit($m->reference ?? '—', 10) }}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
                <p style="margin: 1rem 0 0 0; font-size: 0.875rem;"><a href="{{ route('admin.reports.index', ['tab' => 'movements']) }}">View movement history →</a></p>
            @else
                <div class="empty-state">No recent stock movements.</div>
            @endif
        </div>

        <div id="panel-categories" class="tab-panel">
            @if($categoryBreakdown->isNotEmpty())
                <div class="table-wrap">
                <table class="detail-table">
                    <thead><tr><th>Category</th><th>Total</th><th>In stock</th><th>Out</th><th>Status</th></tr></thead>
                    <tbody>
                    @foreach($categoryBreakdown as $row)
                    <tr>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['total'] }}</td>
                        <td>{{ $row['in_stock'] }}</td>
                        <td>{{ $row['out_of_stock'] }}</td>
                        <td>
                            @php $pct = $row['total'] > 0 ? round(100 * $row['in_stock'] / $row['total']) : 0; @endphp
                            <div>
                                <span class="badge {{ $pct >= 80 ? 'badge-in_stock' : ($pct >= 50 ? 'badge-on_order' : 'badge-out_of_stock') }}">{{ $pct }}%</span>
                                <div class="progress-bar"><span style="width:{{ $pct }}%; background: {{ $pct >= 80 ? '#107C10' : ($pct >= 50 ? '#F7630C' : '#D13438') }};"></span></div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                </div>
            @else
                <div class="empty-state">No categories with products yet.</div>
            @endif
        </div>
    </div>
</div>

{{-- Quick actions --}}
<div class="dash-card" style="margin-top: 1.5rem;">
    <h3 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 600;">Quick actions</h3>
    <div class="quick-actions">
        <a href="{{ route('admin.stock-movements.create') }}" class="quick-action">
            <span class="icon"><svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg></span>
            Record stock movement
        </a>
        <a href="{{ route('admin.products.create') }}" class="quick-action">
            <span class="icon"><svg viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg></span>
            Add product
        </a>
        <a href="{{ route('admin.reports.index') }}" class="quick-action">
            <span class="icon"><svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/></svg></span>
            View reports
        </a>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var tab = this.dataset.tab;
            document.querySelectorAll('.tab-btn').forEach(function(b) { b.classList.remove('active'); });
            document.querySelectorAll('.tab-panel').forEach(function(p) { p.classList.remove('active'); });
            this.classList.add('active');
            var panel = document.getElementById('panel-' + tab);
            if (panel) panel.classList.add('active');
        });
    });

    var searchEl = document.getElementById('searchProducts');
    var noResultsEl = document.getElementById('searchNoResults');
    if (searchEl) {
        searchEl.addEventListener('input', function() {
            var q = this.value.toLowerCase().trim();
            var rows = document.querySelectorAll('#productsTable tbody tr');
            var visible = 0;
            rows.forEach(function(row) {
                var show = !q || row.textContent.toLowerCase().includes(q);
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });
            if (noResultsEl) noResultsEl.style.display = (q && visible === 0) ? 'block' : 'none';
        });
    }

    var stockData = @json($chartStockStatus);
    if (stockData.data.some(function(x) { return x > 0; })) {
        new Chart(document.getElementById('chartStockStatus'), {
            type: 'doughnut',
            data: {
                labels: stockData.labels,
                datasets: [{
                    data: stockData.data,
                    backgroundColor: stockData.colors,
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } }
                }
            }
        });
    } else {
        document.getElementById('chartStockStatus').parentElement.innerHTML = '<div class="empty-state">No stock data yet</div>';
    }

    var movementData = @json($movementByDay);
    var maxVal = Math.max(1, Math.max.apply(null, movementData.flatMap(function(d) { return [d.in, d.out]; })));
    new Chart(document.getElementById('chartMovementTrend'), {
        type: 'bar',
        data: {
            labels: movementData.map(function(d) { return d.date; }),
            datasets: [
                { label: 'In', data: movementData.map(function(d) { return d.in; }), backgroundColor: '#107C10', borderRadius: 6 },
                { label: 'Out', data: movementData.map(function(d) { return d.out; }), backgroundColor: '#D13438', borderRadius: 6 }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, suggestedMax: maxVal + 2 }
            },
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } }
            }
        }
    });
})();
</script>
@endpush
@endsection
