<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'out-of-stock');
        $threshold = (int) ($request->threshold ?? \App\Models\Setting::get('low_stock_threshold') ?? config('inventory.low_stock_threshold', 5));
        $outOfStock = collect();
        $lowStock = collect();
        $movements = collect();

        $products = Product::with(['category', 'stock'])->get();

        if ($tab === 'out-of-stock') {
            $outOfStock = $products->filter(fn ($p) => ($p->stock?->quantity ?? 0) <= 0 || ($p->stock?->status ?? '') === 'out_of_stock');
        } elseif ($tab === 'low-stock') {
            $lowStock = $products->filter(function ($p) use ($threshold) {
                $qty = $p->stock?->quantity ?? 0;
                $reorderPoint = $p->reorder_point;
                if ($reorderPoint !== null) {
                    return $qty <= $reorderPoint && $qty > 0;
                }
                return $qty <= $threshold && $qty > 0;
            });
        } else {
            $query = StockMovement::with(['product', 'supplier', 'user']);
            if ($request->supplier_id) {
                $query->where('supplier_id', $request->supplier_id);
            }
            if ($request->from_date) {
                $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
            }
            if ($request->to_date) {
                $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
            }
            $movements = $query->with('supplier')->orderBy('created_at', 'desc')->limit(300)->get();
        }

        // Summary stats for report header
        $summary = [
            'total_products' => $products->count(),
            'in_stock' => $products->filter(fn ($p) => ($p->stock?->quantity ?? 0) > 0)->count(),
            'out_of_stock' => $products->filter(fn ($p) => ($p->stock?->quantity ?? 0) <= 0)->count(),
        ];

        // Category breakdown
        $suppliers = \App\Models\Supplier::orderBy('name')->get();

        $categoryBreakdown = Category::orderBy('sort_order')->get()->map(function ($cat) use ($products) {
            $catsProducts = $products->where('category_id', $cat->id);
            $total = $catsProducts->count();
            $inStock = $catsProducts->filter(fn ($p) => ($p->stock?->quantity ?? 0) > 0)->count();
            $outOfStock = $catsProducts->filter(fn ($p) => ($p->stock?->quantity ?? 0) <= 0)->count();
            return [
                'name' => $cat->name,
                'total' => $total,
                'in_stock' => $inStock,
                'out_of_stock' => $outOfStock,
            ];
        })->filter(fn ($c) => $c['total'] > 0);

        // Movement totals for date range (movements tab only)
        $movementTotals = ['in' => 0, 'out' => 0];
        if ($tab === 'movements') {
            $movementTotals['in'] = $movements->whereIn('type', ['in', 'adjustment'])->sum('quantity');
            $movementTotals['out'] = $movements->where('type', 'out')->sum('quantity');
        }

        return view('admin.reports.index', compact('outOfStock', 'lowStock', 'movements', 'tab', 'summary', 'categoryBreakdown', 'threshold', 'movementTotals', 'suppliers'));
    }

    public function export(Request $request)
    {
        $tab = $request->get('tab', 'out-of-stock');
        $threshold = (int) ($request->threshold ?? \App\Models\Setting::get('low_stock_threshold') ?? config('inventory.low_stock_threshold', 5));
        $products = Product::with(['category', 'stock'])->get();

        $headers = ['Content-Type' => 'text/csv', 'Content-Disposition' => 'attachment; filename="inventory-report-' . $tab . '-' . date('Y-m-d') . '.csv"'];

        $callback = function () use ($tab, $threshold, $products, $request) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['S3VT Inventory Report - ' . $tab . ' - ' . date('Y-m-d H:i')]);

            if ($tab === 'out-of-stock') {
                $items = $products->filter(fn ($p) => ($p->stock?->quantity ?? 0) <= 0 || ($p->stock?->status ?? '') === 'out_of_stock');
                fputcsv($out, ['SKU', 'Name', 'Category', 'Quantity']);
                foreach ($items as $p) {
                    fputcsv($out, [$p->sku, $p->name, $p->category?->name ?? '', $p->stock?->quantity ?? 0]);
                }
            } elseif ($tab === 'low-stock') {
                $items = $products->filter(function ($p) use ($threshold) {
                    $qty = $p->stock?->quantity ?? 0;
                    $reorderPoint = $p->reorder_point;
                    if ($reorderPoint !== null) {
                        return $qty <= $reorderPoint && $qty > 0;
                    }
                    return $qty <= $threshold && $qty > 0;
                });
                fputcsv($out, ['SKU', 'Name', 'Category', 'Quantity', 'Reorder Point']);
                foreach ($items as $p) {
                    fputcsv($out, [$p->sku, $p->name, $p->category?->name ?? '', $p->stock?->quantity ?? 0, $p->reorder_point ?? '']);
                }
            } else {
                $query = StockMovement::with(['product', 'supplier']);
                if ($request->supplier_id) {
                    $query->where('supplier_id', $request->supplier_id);
                }
                if ($request->from_date) {
                    $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
                }
                if ($request->to_date) {
                    $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
                }
                $movements = $query->orderBy('created_at', 'desc')->limit(500)->get();
                fputcsv($out, ['Date', 'Product', 'SKU', 'Type', 'Quantity', 'Supplier', 'Reference']);
                foreach ($movements as $m) {
                    fputcsv($out, [$m->created_at->format('Y-m-d H:i'), $m->product->name, $m->product->sku, $m->type, $m->quantity, $m->supplier?->name ?? '', $m->reference ?? '']);
                }
            }

            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
