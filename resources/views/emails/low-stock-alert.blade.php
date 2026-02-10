<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alert – S3VT Inventory</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.5; color: #334155; margin: 0; padding: 0; background: #f8fafc; }
        .container { max-width: 600px; margin: 0 auto; padding: 24px; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.08); overflow: hidden; }
        .header { background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%); color: #fff; padding: 20px 24px; }
        .header h1 { margin: 0; font-size: 1.25rem; font-weight: 600; }
        .header p { margin: 4px 0 0 0; font-size: 0.875rem; opacity: 0.95; }
        .content { padding: 24px; }
        .section { margin-bottom: 20px; }
        .section:last-child { margin-bottom: 0; }
        .section-title { font-size: 0.8125rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 10px; }
        .section.out-of-stock .section-title { color: #dc2626; }
        .section.low-stock .section-title { color: #b45309; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9375rem; }
        th, td { padding: 8px 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { font-weight: 600; color: #475569; font-size: 0.8125rem; }
        tr:last-child td { border-bottom: none; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; }
        .badge-out { background: #fef2f2; color: #dc2626; }
        .badge-low { background: #fef3c7; color: #b45309; }
        .btn { display: inline-block; padding: 10px 20px; background: #0f766e; color: #fff !important; text-decoration: none; border-radius: 8px; font-weight: 500; font-size: 0.9375rem; margin-top: 16px; }
        .btn:hover { background: #0d9488; }
        .footer { padding: 16px 24px; background: #f8fafc; font-size: 0.8125rem; color: #64748b; }
        .footer a { color: #0f766e; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>⚠️ Low Stock Alert</h1>
                <p>{{ $outOfStock->count() + $lowStock->count() }} product(s) need attention</p>
            </div>
            <div class="content">
                @if($outOfStock->isNotEmpty())
                <div class="section out-of-stock">
                    <div class="section-title">Out of stock ({{ $outOfStock->count() }})</div>
                    <table>
                        <thead><tr><th>SKU</th><th>Product</th><th>Category</th></tr></thead>
                        <tbody>
                        @foreach($outOfStock as $p)
                        <tr>
                            <td><code>{{ $p->sku }}</code></td>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->category?->name ?? '—' }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                @if($lowStock->isNotEmpty())
                <div class="section low-stock">
                    <div class="section-title">Below reorder point ({{ $lowStock->count() }})</div>
                    <table>
                        <thead><tr><th>SKU</th><th>Product</th><th>Qty</th><th>Reorder</th><th>Category</th></tr></thead>
                        <tbody>
                        @foreach($lowStock as $p)
                        <tr>
                            <td><code>{{ $p->sku }}</code></td>
                            <td>{{ $p->name }}</td>
                            <td><span class="badge badge-low">{{ $p->stock?->quantity ?? 0 }}</span></td>
                            <td>{{ $p->reorder_point ?? '—' }}</td>
                            <td>{{ $p->category?->name ?? '—' }}</td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

                <a href="{{ config('app.url') }}/admin" class="btn">View dashboard & record stock</a>
            </div>
            <div class="footer">
                This alert was sent by S3VT Inventory. Configure alerts in <a href="{{ config('app.url') }}/admin/settings">Settings</a>.
            </div>
        </div>
    </div>
</body>
</html>
