<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'out-of-stock');
        $outOfStock = collect();
        $lowStock = collect();
        $movements = collect();

        if ($tab === 'out-of-stock') {
            $outOfStock = Product::with(['category', 'stock'])
                ->get()
                ->filter(fn ($p) => ($p->stock?->quantity ?? 0) <= 0 || ($p->stock?->status ?? '') === 'out_of_stock');
        } elseif ($tab === 'low-stock') {
            $threshold = (int) ($request->threshold ?? 0);
            $lowStock = Product::with(['category', 'stock'])
                ->get()
                ->filter(fn ($p) => ($p->stock?->quantity ?? 0) <= $threshold);
        } else {
            $query = StockMovement::with(['product', 'user']);
            if ($request->from_date) {
                $query->where('created_at', '>=', $request->from_date);
            }
            if ($request->to_date) {
                $query->where('created_at', '<=', $request->to_date);
            }
            $movements = $query->orderBy('created_at', 'desc')->limit(200)->get();
        }

        return view('admin.reports.index', compact('outOfStock', 'lowStock', 'movements', 'tab'));
    }
}
