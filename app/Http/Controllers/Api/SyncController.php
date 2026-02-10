<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function products(Request $request)
    {
        $query = Product::with(['category', 'stock']);
        if ($request->category) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }
        $limit = min((int) ($request->limit ?? 100), 500);
        $offset = (int) ($request->offset ?? 0);
        $products = $query->orderBy('name')->limit($limit)->offset($offset)->get();
        return $products->map(fn ($p) => $this->formatSyncProduct($p));
    }

    public function product(Product $product)
    {
        $product->load(['category', 'stock']);
        return $this->formatSyncProduct($product);
    }

    public function productBySlug(string $slug)
    {
        $product = Product::with(['category', 'stock'])->where('slug', $slug)->firstOrFail();
        return $this->formatSyncProduct($product);
    }

    public function categories()
    {
        return Category::orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug', 'image_url', 'sort_order']);
    }

    private function formatSyncProduct(Product $product): array
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
