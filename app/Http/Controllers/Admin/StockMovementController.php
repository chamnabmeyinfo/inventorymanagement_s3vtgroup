<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function create()
    {
        $products = Product::with('stock')->orderBy('name')->get();
        return view('admin.stock-movements.form', compact('products'));
    }

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
            return back()->withErrors(['quantity' => "Insufficient stock. Current: {$currentQty}"])->withInput();
        }

        $newQty = in_array($type, ['in', 'adjustment']) ? $currentQty + $qty : $currentQty - $qty;
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

        return redirect()->route('admin.stock-movements.create')->with('success', 'Movement recorded.');
    }

    public function history(Product $product)
    {
        $movements = $product->stockMovements()->with('user')->orderBy('created_at', 'desc')->limit(200)->get();
        return view('admin.stock-movements.history', compact('product', 'movements'));
    }
}
