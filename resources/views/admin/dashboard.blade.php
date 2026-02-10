@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<style>
    .dashboard-tabs { display: flex; gap: 0.25rem; margin-bottom: 1rem; }
    .dashboard-tabs a { padding: 0.4rem 0.75rem; border-radius: 6px; font-size: 0.875rem; background: #e2e8f0; color: #475569; }
    .dashboard-tabs a:hover { background: #cbd5e1; }
    .dashboard-tabs a.active { background: #2563eb; color: #fff; }
    .chart-container { position: relative; height: 220px; margin-top: 0.5rem; }
    .report-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; }
    .detail-table { font-size: 0.875rem; }
    .detail-table th { white-space: nowrap; }
    .detail-table td { vertical-align: middle; }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }
    .tab-buttons { display: flex; gap: 0.25rem; margin-bottom: 1rem; border-bottom: 1px solid #e2e8f0; }
    .tab-buttons button { padding: 0.5rem 1rem; border: none; background: none; cursor: pointer; font: inherit; color: #64748b; border-bottom: 2px solid transparent; margin-bottom: -1px; }
    .tab-buttons button:hover { color: #2563eb; }
    .tab-buttons button.active { color: #2563eb; font-weight: 600; border-bottom-color: #2563eb; }
    .search-box { padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 6px; width: 100%; max-width: 280px; font-size: 0.875rem; }
    .stat-card { transition: transform 0.15s; }
    .stat-card:hover { transform: translateY(-2px); }
    .top-mover { display: flex; justify-content: space-between; align-items: center; padding: 0.5rem 0; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; }
    .top-mover:last-child { border-bottom: none; }
    .no-results { padding: 1rem; color: #64748b; font-size: 0.875rem; display: none; }
</style>
@endpush

@section('content')
{{-- Date range filter --}}
<div class="card" style="margin-bottom: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h2 style="margin: 0;">Inventory Dashboard</h2>
            @if($lastMovementAt ?? null)
            <p style="margin: 0.25rem 0 0 0; font-size: 0.8125rem; color: #64748b;">Last activity: {{ $lastMovementAt->diffForHumans() }}</p>
            @endif
        </div>
        <div class="dashboard-tabs">
            <a href="{{ route('admin.dashboard', ['days' => 7]) }}" class="{{ ($days ?? 7) == 7 ? 'active' : '' }}">7 days</a>
            <a href="{{ route('admin.dashboard', ['days' => 14]) }}" class="{{ ($days ?? 7) == 14 ? 'active' : '' }}">14 days</a>
            <a href="{{ route('admin.dashboard', ['days' => 30]) }}" class="{{ ($days ?? 7) == 30 ? 'active' : '' }}">30 days</a>
        </div>
    </div>
</div>

{{-- Smart Alert Banner --}}
@if($outOfStockCount > 0 || $lowStockCount > 0)
<div class="card" style="border-left: 4px solid #dc2626; background: #fef2f2;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 style="margin: 0 0 0.25rem 0; color: #991b1b;">{{ $outOfStockCount + $lowStockCount }} products need attention</h3>
            <p style="margin: 0; color: #b91c1c; font-size: 0.9375rem;">
                @if($outOfStockCount > 0)
                    {{ $outOfStockCount }} out of stock
                @endif
                @if($outOfStockCount > 0 && $lowStockCount > 0)
                    ·
                @endif
                @if($lowStockCount > 0)
                    {{ $lowStockCount }} below reorder point
                @endif
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.stock-movements.create') }}" class="btn btn-primary">Record stock in</a>
            <a href="{{ route('admin.reports.index', ['tab' => 'out-of-stock']) }}" class="btn btn-secondary">View all</a>
        </div>
    </div>
</div>
@endif

{{-- Quick Stats --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
    <div class="card stat-card" style="padding: 1rem; text-align: center;">
        <div style="font-size: 1.75rem; font-weight: 700; color: #0f172a;">{{ $totalProducts }}</div>
        <div style="font-size: 0.875rem; color: #64748b;">Total products</div>
    </div>
    <div class="card stat-card" style="padding: 1rem; text-align: center;">
        <div style="font-size: 1.75rem; font-weight: 700; color: #166534;">{{ $inStockCount }}</div>
        <div style="font-size: 0.875rem; color: #64748b;">In stock</div>
    </div>
    <div class="card stat-card" style="padding: 1rem; text-align: center;">
        <div style="font-size: 1.75rem; font-weight: 700; color: #dc2626;">{{ $outOfStockCount }}</div>
        <div style="font-size: 0.875rem; color: #64748b;">Out of stock</div>
    </div>
    <div class="card stat-card" style="padding: 1rem; text-align: center;">
        <div style="font-size: 1.75rem; font-weight: 700; color: #b45309;">{{ $lowStockCount }}</div>
        <div style="font-size: 0.875rem; color: #64748b;">Low stock</div>
    </div>
    <div class="card stat-card" style="padding: 1rem; text-align: center;">
        <div style="font-size: 1rem; font-weight: 700; color: #2563eb;">+{{ $recentInCount }} / -{{ $recentOutCount }}</div>
        <div style="font-size: 0.875rem; color: #64748b;">Last {{ $days }} days (in/out)</div>
    </div>
</div>

{{-- Charts + Top movers --}}
<div class="report-grid" style="margin-bottom: 1.5rem;">
    <div class="card">
        <h3 style="margin: 0 0 0.5rem 0;">Stock status</h3>
        <div class="chart-container">
            <canvas id="chartStockStatus"></canvas>
        </div>
    </div>
    <div class="card">
        <h3 style="margin: 0 0 0.5rem 0;">Movement trend (last {{ $days }} days)</h3>
        <div class="chart-container">
            <canvas id="chartMovementTrend"></canvas>
        </div>
    </div>
    @if($topMovers->isNotEmpty())
    <div class="card">
        <h3 style="margin: 0 0 0.5rem 0;">Top movers</h3>
        <p style="margin: 0 0 0.5rem 0; font-size: 0.8125rem; color: #64748b;">Most activity in last {{ $days }} days</p>
        @foreach($topMovers as $m)
        <div class="top-mover">
            <a href="{{ route('admin.products.edit', $m->product) }}">{{ $m->product->name }}</a>
            <span style="color: #64748b;">+{{ $m->total_in }} / -{{ $m->total_out }}</span>
        </div>
        @endforeach
    </div>
    @endif
    @if(($stockBySupplier ?? collect())->isNotEmpty())
    <div class="card">
        <h3 style="margin: 0 0 0.5rem 0;">Stock from suppliers</h3>
        <p style="margin: 0 0 0.5rem 0; font-size: 0.8125rem; color: #64748b;">Received in last {{ $days }} days</p>
        @foreach($stockBySupplier as $r)
        <div class="top-mover">
            <a href="{{ route('admin.suppliers.edit', $r->supplier) }}">{{ $r->supplier->name }}</a>
            <span style="color: #166534;">+{{ $r->total }}</span>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Tabbed detail reports --}}
<div class="card">
    <div class="tab-buttons">
        <button type="button" class="tab-btn active" data-tab="products">Products needing attention</button>
        <button type="button" class="tab-btn" data-tab="activity">Recent activity</button>
        <button type="button" class="tab-btn" data-tab="categories">Category breakdown</button>
    </div>

    <div id="panel-products" class="tab-panel active">
        <div style="margin-bottom: 0.75rem; display: flex; gap: 0.5rem; align-items: center;">
            <input type="text" class="search-box" id="searchProducts" placeholder="Search SKU, name...">
            <span id="searchNoResults" class="no-results">No matches</span>
        </div>
        @if($productsNeedingAttention->isNotEmpty())
            <table class="detail-table" id="productsTable">
                <thead>
                    <tr><th>SKU</th><th>Name</th><th>Category</th><th>Qty</th><th>Reorder</th><th>Supplier</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($productsNeedingAttention as $p)
                    <tr>
                        <td>{{ $p->sku }}</td>
                        <td>{{ $p->name }}</td>
                        <td>{{ $p->category?->name ?? '—' }}</td>
                        <td><strong>{{ $p->stock?->quantity ?? 0 }}</strong></td>
                        <td>{{ $p->reorder_point ?? '—' }}</td>
                        <td>@if($p->preferredSupplier)<a href="{{ route('admin.suppliers.edit', $p->preferredSupplier) }}">{{ $p->preferredSupplier->name }}</a>@else—@endif</td>
                        <td>
                            <a href="{{ route('admin.stock-movements.create') }}?product_id={{ $p->id }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.8125rem;">Record</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #64748b; margin: 0;">All products are adequately stocked.</p>
        @endif
        <p style="margin: 0.75rem 0 0 0; font-size: 0.875rem;">
            <a href="{{ route('admin.reports.index', ['tab' => 'out-of-stock']) }}">View full report →</a>
        </p>
    </div>

    <div id="panel-activity" class="tab-panel">
        @if($recentMovements->isNotEmpty())
            <table class="detail-table">
                <thead>
                    <tr><th>When</th><th>Product</th><th>SKU</th><th>Type</th><th>Qty</th><th>Supplier</th><th>Reference</th></tr>
                </thead>
                <tbody>
                    @foreach($recentMovements as $m)
                    <tr>
                        <td style="color: #64748b;">{{ $m->created_at->format('M j, H:i') }}</td>
                        <td><a href="{{ route('admin.products.edit', $m->product) }}">{{ $m->product->name }}</a></td>
                        <td>{{ $m->product->sku }}</td>
                        <td><span class="badge badge-{{ $m->type === 'in' ? 'in_stock' : ($m->type === 'out' ? 'out_of_stock' : 'on_order') }}">{{ $m->type }}</span></td>
                        <td>{{ $m->quantity }}</td>
                        <td>@if($m->supplier)<a href="{{ route('admin.suppliers.edit', $m->supplier) }}">{{ $m->supplier->name }}</a>@else—@endif</td>
                        <td>{{ $m->reference ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #64748b; margin: 0;">No recent stock movements.</p>
        @endif
        <p style="margin: 0.75rem 0 0 0; font-size: 0.875rem;">
            <a href="{{ route('admin.reports.index', ['tab' => 'movements']) }}">View movement history →</a>
        </p>
    </div>

    <div id="panel-categories" class="tab-panel">
        @if($categoryBreakdown->isNotEmpty())
            <table class="detail-table">
                <thead>
                    <tr><th>Category</th><th>Total</th><th>In stock</th><th>Out of stock</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach($categoryBreakdown as $row)
                    <tr>
                        <td>{{ $row['name'] }}</td>
                        <td>{{ $row['total'] }}</td>
                        <td>{{ $row['in_stock'] }}</td>
                        <td>{{ $row['out_of_stock'] }}</td>
                        <td>
                            @php $pct = $row['total'] > 0 ? round(100 * $row['in_stock'] / $row['total']) : 0; @endphp
                            <span class="badge {{ $pct >= 80 ? 'badge-in_stock' : ($pct >= 50 ? 'badge-on_order' : 'badge-out_of_stock') }}">{{ $pct }}% in stock</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #64748b; margin: 0;">No categories with products yet.</p>
        @endif
    </div>
</div>

{{-- Quick actions --}}
<div class="card" style="margin-top: 1rem;">
    <h3 style="margin: 0 0 0.75rem 0;">Quick actions</h3>
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
        <a href="{{ route('admin.stock-movements.create') }}" class="btn btn-primary">Record stock movement</a>
        <a href="{{ route('admin.products.create') }}" class="btn btn-secondary">Add product</a>
        <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">View full reports</a>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
    // Tab switching
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

    // Search filter
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

    // Charts
    var stockData = @json($chartStockStatus);
    if (stockData.data.some(function(x) { return x > 0; })) {
        new Chart(document.getElementById('chartStockStatus'), {
            type: 'doughnut',
            data: {
                labels: stockData.labels,
                datasets: [{
                    data: stockData.data,
                    backgroundColor: stockData.colors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    } else {
        document.getElementById('chartStockStatus').parentElement.innerHTML = '<p style="color:#64748b;margin:2rem 0;text-align:center;">No stock data yet</p>';
    }

    var movementData = @json($movementByDay);
    var maxVal = Math.max(1, Math.max.apply(null, movementData.flatMap(function(d) { return [d.in, d.out]; })));
    new Chart(document.getElementById('chartMovementTrend'), {
        type: 'bar',
        data: {
            labels: movementData.map(function(d) { return d.date; }),
            datasets: [
                { label: 'In', data: movementData.map(function(d) { return d.in; }), backgroundColor: '#166534' },
                { label: 'Out', data: movementData.map(function(d) { return d.out; }), backgroundColor: '#dc2626' }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { stacked: false },
                y: { beginAtZero: true, suggestedMax: maxVal + 2 }
            },
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
})();
</script>
@endpush
@endsection
