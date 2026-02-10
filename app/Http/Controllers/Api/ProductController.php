<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'stock']);
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%");
            });
        }
        $sort = $request->get('sort', 'created_at');
        $allowed = ['name', 'sku', 'created_at'];
        $sort = in_array($sort, $allowed) ? $sort : 'created_at';
        $products = $query->orderBy($sort)->get();
        return $products->map(fn ($p) => $this->formatProduct($p));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sku' => 'required|string|max:64|unique:products,sku',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'price_display_type' => 'required|in:on_request,fixed',
            'price_amount' => 'nullable|numeric|min:0',
            'image_urls' => 'nullable|array',
            'related_product_ids' => 'nullable|array',
            'stock_status' => 'nullable|in:in_stock,on_order,out_of_stock',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $product = Product::create(collect($validated)->except(['stock_status', 'stock_quantity'])->toArray());
        Stock::updateOrCreate(
            ['product_id' => $product->id],
            [
                'quantity' => $validated['stock_quantity'] ?? 0,
                'status' => $validated['stock_status'] ?? 'out_of_stock',
            ]
        );
        return response()->json($this->formatProduct($product->load(['category', 'stock'])), 201);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'stock']);
        return $this->formatProduct($product);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => 'sometimes|string|max:64|unique:products,sku,' . $product->id,
            'name' => 'sometimes|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $product->id,
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'specifications' => 'nullable|array',
            'price_display_type' => 'sometimes|in:on_request,fixed',
            'price_amount' => 'nullable|numeric|min:0',
            'image_urls' => 'nullable|array',
            'related_product_ids' => 'nullable|array',
            'stock_status' => 'nullable|in:in_stock,on_order,out_of_stock',
            'stock_quantity' => 'nullable|integer|min:0',
        ]);
        $product->update(collect($validated)->except(['stock_status', 'stock_quantity'])->toArray());
        if (isset($validated['stock_status']) || isset($validated['stock_quantity'])) {
            Stock::updateOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity' => $validated['stock_quantity'] ?? $product->stock?->quantity ?? 0,
                    'status' => $validated['stock_status'] ?? $product->stock?->status ?? 'out_of_stock',
                ]
            );
        }
        return $this->formatProduct($product->load(['category', 'stock']));
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }

    private function formatProduct(Product $product): array
    {
        $s = $product->stock;
        return array_merge($product->toArray(), [
            'category_name' => $product->category?->name,
            'category_slug' => $product->category?->slug,
            'stock_quantity' => $s?->quantity ?? 0,
            'stock_status' => $s?->status ?? 'out_of_stock',
        ]);
    }
}
