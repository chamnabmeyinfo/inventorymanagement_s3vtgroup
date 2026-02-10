<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $lowStockThreshold = (int) (\App\Models\Setting::get('low_stock_threshold') ?? config('inventory.low_stock_threshold', 5));
        $days = max(1, min(90, (int) ($request->get('days', 7))));

        $products = Product::with(['category', 'stock'])->get();

        // Products needing attention
        $productsNeedingAttention = $products
            ->load('preferredSupplier')
            ->filter(function ($p) use ($lowStockThreshold) {
                $qty = $p->stock?->quantity ?? 0;
                $reorderPoint = $p->reorder_point;
                if ($reorderPoint !== null) {
                    return $qty <= $reorderPoint;
                }
                return $qty <= $lowStockThreshold || ($p->stock?->status ?? '') === 'out_of_stock';
            })
            ->take(15);

        $outOfStockCount = $products->filter(fn ($p) => ($p->stock?->quantity ?? 0) <= 0 || ($p->stock?->status ?? '') === 'out_of_stock')->count();
        $lowStockCount = $products->filter(function ($p) use ($lowStockThreshold) {
            $qty = $p->stock?->quantity ?? 0;
            if ($qty <= 0) {
                return false;
            }
            $reorderPoint = $p->reorder_point;
            if ($reorderPoint !== null) {
                return $qty <= $reorderPoint;
            }
            return $qty <= $lowStockThreshold;
        })->count();
        $inStockCount = $products->filter(fn ($p) => ($p->stock?->quantity ?? 0) > 0 && ($p->stock?->status ?? '') !== 'out_of_stock')->count();
        $totalProducts = $products->count();

        $recentMovements = StockMovement::with(['product', 'supplier', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

        $lastMovementAt = StockMovement::orderBy('created_at', 'desc')->value('created_at');

        $since = now()->subDays($days);
        $recentOutCount = StockMovement::where('type', 'out')->where('created_at', '>=', $since)->sum('quantity');
        $recentInCount = StockMovement::whereIn('type', ['in', 'adjustment'])->where('created_at', '>=', $since)->sum('quantity');

        // Category breakdown
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

        // Chart data: stock status pie (Windows 11 colors)
        $chartStockStatus = [
            'labels' => ['In stock', 'Low stock', 'Out of stock'],
            'data' => [$inStockCount, $lowStockCount, $outOfStockCount],
            'colors' => ['#107C10', '#F7630C', '#D13438'],
        ];

        // Chart data: movements by day (last N days)
        $movementByDay = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('M j');
            $dayStart = now()->subDays($i)->startOfDay();
            $dayEnd = now()->subDays($i)->endOfDay();
            $in = StockMovement::whereIn('type', ['in', 'adjustment'])
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->sum('quantity');
            $out = StockMovement::where('type', 'out')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->sum('quantity');
            $movementByDay[] = [
                'date' => $date,
                'in' => (int) $in,
                'out' => (int) $out,
            ];
        }

        // Top movers (products with most movement activity in period)
        $topMoversRaw = DB::select(
            'SELECT product_id, SUM(CASE WHEN type IN (?, ?) THEN quantity ELSE 0 END) as total_in, SUM(CASE WHEN type = ? THEN quantity ELSE 0 END) as total_out FROM stock_movements WHERE created_at >= ? GROUP BY product_id',
            ['in', 'adjustment', 'out', $since]
        );
        // Stock received by supplier (last N days)
        $stockBySupplier = collect(
            DB::select(
                'SELECT supplier_id, SUM(quantity) as total FROM stock_movements WHERE type IN (?, ?) AND created_at >= ? AND supplier_id IS NOT NULL GROUP BY supplier_id ORDER BY total DESC LIMIT 5',
                ['in', 'adjustment', $since]
            )
        )->map(function ($row) {
            $s = Supplier::find($row->supplier_id);
            $row->supplier = $s;
            $row->total = (int) $row->total;
            return $row;
        })->filter(fn ($r) => $r->supplier);

        $topMovers = collect($topMoversRaw)
            ->sortByDesc(fn ($r) => (int) $r->total_in + (int) $r->total_out)
            ->take(5)
            ->values()
            ->map(function ($row) {
                $p = Product::find($row->product_id);
                if (!$p) {
                    return null;
                }
                $row->product = $p;
                $row->total_in = (int) $row->total_in;
                $row->total_out = (int) $row->total_out;
                return $row;
            })
            ->filter();

        return view('admin.dashboard', compact(
            'productsNeedingAttention',
            'outOfStockCount',
            'lowStockCount',
            'inStockCount',
            'totalProducts',
            'recentMovements',
            'recentOutCount',
            'recentInCount',
            'categoryBreakdown',
            'chartStockStatus',
            'movementByDay',
            'days',
            'topMovers',
            'lastMovementAt',
            'stockBySupplier'
        ));
    }
}
