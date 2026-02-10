<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
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
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
        }
        $products = $query->with('preferredSupplier')->orderBy('name')->paginate(20);
        $categories = Category::orderBy('sort_order')->get();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('sort_order')->get();
        $suppliers = \App\Models\Supplier::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.products.form', ['product' => null, 'categories' => $categories, 'suppliers' => $suppliers]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $product = Product::create(collect($validated)->except(['stock_status', 'stock_quantity'])->toArray());
        $product->stock()->create([
            'quantity' => $validated['stock_quantity'] ?? 0,
            'status' => $validated['stock_status'] ?? 'out_of_stock',
        ]);
        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $product->load('stock');
        $categories = Category::orderBy('sort_order')->get();
        $suppliers = \App\Models\Supplier::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.products.form', ['product' => $product, 'categories' => $categories, 'suppliers' => $suppliers]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product);
        $product->update(collect($validated)->except(['stock_status', 'stock_quantity'])->toArray());
        if (isset($validated['stock_status']) || isset($validated['stock_quantity'])) {
            $product->stock()->updateOrCreate(
                ['product_id' => $product->id],
                [
                    'quantity' => $validated['stock_quantity'] ?? $product->stock?->quantity ?? 0,
                    'status' => $validated['stock_status'] ?? $product->stock?->status ?? 'out_of_stock',
                ]
            );
        }
        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function validateProduct(Request $request, ?Product $product = null): array
    {
        $rules = [
            'sku' => 'required|string|max:64',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'price_display_type' => 'required|in:on_request,fixed',
            'price_amount' => 'nullable|numeric|min:0',
            'stock_status' => 'nullable|in:in_stock,on_order,out_of_stock',
            'stock_quantity' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'preferred_supplier_id' => 'nullable|exists:suppliers,id',
        ];
        if ($product) {
            $rules['sku'] .= '|unique:products,sku,' . $product->id;
            $rules['slug'] .= '|unique:products,slug,' . $product->id;
        } else {
            $rules['sku'] .= '|unique:products,sku';
            $rules['slug'] .= '|unique:products,slug';
        }
        return $request->validate($rules);
    }
}
