<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function outOfStock()
    {
        return Product::with(['category', 'stock'])
            ->where(function ($q) {
                $q->whereDoesntHave('stock')
                    ->orWhereHas('stock', fn ($q) => $q->where('status', 'out_of_stock')->orWhere('quantity', '<=', 0));
            })
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'slug' => $p->slug,
                'category_id' => $p->category_id,
                'category_name' => $p->category?->name,
                'quantity' => $p->stock?->quantity ?? 0,
            ]);
    }

    public function lowStock(Request $request)
    {
        $threshold = (int) ($request->threshold ?? 0);
        return Product::with(['category', 'stock'])
            ->get()
            ->filter(fn ($p) => ($p->stock?->quantity ?? 0) <= $threshold)
            ->map(fn ($p) => [
                'id' => $p->id,
                'sku' => $p->sku,
                'name' => $p->name,
                'slug' => $p->slug,
                'category_id' => $p->category_id,
                'category_name' => $p->category?->name,
                'quantity' => $p->stock?->quantity ?? 0,
                'status' => $p->stock?->status ?? 'out_of_stock',
            ])
            ->values();
    }

    public function movementSummary(Request $request)
    {
        $query = StockMovement::selectRaw('type, COUNT(*) as count, SUM(quantity) as total_quantity')->groupBy('type');
        if ($request->from_date) {
            $query->where('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->where('created_at', '<=', $request->to_date);
        }
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        return $query->get();
    }
}
