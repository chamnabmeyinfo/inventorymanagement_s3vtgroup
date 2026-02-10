<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment,transfer',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $stock = $product->stock ?? new Stock(['product_id' => $product->id, 'quantity' => 0, 'status' => 'out_of_stock']);
        $currentQty = $stock->quantity ?? 0;

        $type = $validated['type'];
        $qty = (int) $validated['quantity'];
        if (in_array($type, ['out', 'transfer']) && $currentQty < $qty) {
            return response()->json([
                'error' => 'Insufficient stock',
                'current' => $currentQty,
                'requested' => $qty,
            ], 400);
        }

        $newQty = $currentQty;
        if (in_array($type, ['in', 'adjustment'])) {
            $newQty += $qty;
        } else {
            $newQty -= $qty;
        }

        $newStatus = $stock->status ?? 'out_of_stock';
        if ($newQty <= 0) {
            $newStatus = 'out_of_stock';
        } elseif ($newStatus === 'out_of_stock') {
            $newStatus = 'in_stock';
        }

        StockMovement::create([
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $qty,
            'reference' => $validated['reference'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'user_id' => $request->user()->id,
        ]);

        Stock::updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => $newQty, 'status' => $newStatus]
        );

        $movement = StockMovement::with('product')->latest()->first();
        return response()->json($movement, 201);
    }

    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);
        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->from_date) {
            $query->where('created_at', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->where('created_at', '<=', $request->to_date);
        }
        $limit = min((int) ($request->limit ?? 100), 500);
        return $query->orderBy('created_at', 'desc')->limit($limit)->get();
    }

    public function byProduct(Product $product)
    {
        return StockMovement::where('product_id', $product->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(200)
            ->get()
            ->map(fn ($m) => array_merge($m->toArray(), ['user_name' => $m->user?->name]));
    }
}
