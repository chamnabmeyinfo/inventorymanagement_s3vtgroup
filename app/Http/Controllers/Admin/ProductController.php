<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        return view('admin.products.form', ['product' => null, 'categories' => $categories, 'suppliers' => $suppliers, 'existingImages' => []]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);
        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $product = Product::create(collect($validated)->except(['stock_status', 'stock_quantity', 'existing_images', 'images'])->toArray());
        $product->stock()->create([
            'quantity' => $validated['stock_quantity'] ?? 0,
            'status' => $validated['stock_status'] ?? 'out_of_stock',
        ]);
        $imageUrls = $this->processImageUploads($request, $product);
        if (!empty($imageUrls)) {
            $product->update(['image_urls' => $imageUrls]);
        }
        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $product->load('stock');
        $categories = Category::orderBy('sort_order')->get();
        $suppliers = \App\Models\Supplier::orderBy('sort_order')->orderBy('name')->get();
        $existingImages = collect($product->image_urls ?? [])->filter(fn ($p) => $p && Storage::disk('public')->exists($p))->values()->toArray();
        return view('admin.products.form', ['product' => $product, 'categories' => $categories, 'suppliers' => $suppliers, 'existingImages' => $existingImages]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request, $product);
        $imageUrls = $this->processImageUploads($request, $product);
        $product->update(collect($validated)->except(['stock_status', 'stock_quantity', 'existing_images', 'images'])->toArray());
        $product->update(['image_urls' => $imageUrls]);
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

    public function duplicate(Product $product)
    {
        $product->load('stock');
        $copy = $product->replicate();
        $copy->sku = $this->uniqueSku($product->sku);
        $copy->name = $product->name . ' (Copy)';
        $copy->slug = Str::slug($copy->name) . '-' . substr(uniqid(), -4);
        $copy->save();
        $copy->stock()->create([
            'quantity' => 0,
            'status' => 'out_of_stock',
        ]);
        return redirect()->route('admin.products.edit', $copy)->with('success', 'Product duplicated. Edit and save.');
    }

    private function uniqueSku(string $sku): string
    {
        $base = preg_replace('/-copy-\d+$/i', '', $sku);
        $base = preg_replace('/-copy$/i', '', $base);
        $i = 1;
        while (Product::where('sku', $new = $base . '-COPY-' . $i)->exists()) {
            $i++;
        }
        return $new;
    }

    private function processImageUploads(Request $request, Product $product): array
    {
        $imageUrls = [];
        $validPaths = collect($product->image_urls ?? [])->filter(fn ($p) => Storage::disk('public')->exists($p))->toArray();

        // Keep existing images that user did not remove
        $existing = $request->input('existing_images', []);
        if (is_array($existing)) {
            foreach ($existing as $path) {
                if (is_string($path) && Storage::disk('public')->exists($path)) {
                    $imageUrls[] = $path;
                }
            }
        }

        // Upload new images
        $files = $request->file('images', []);
        if (!is_array($files)) {
            $files = $files ? [$files] : [];
        }
        $dir = 'products/' . $product->id;
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs($dir, $name, 'public');
                $imageUrls[] = $path;
            }
        }

        return array_values(array_unique($imageUrls));
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
        $validated = $request->validate($rules);

        // Validate image files
        $files = $request->file('images', []);
        if (!is_array($files)) {
            $files = $files ? [$files] : [];
        }
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                    abort(422, 'Invalid image type. Allowed: JPG, PNG, GIF, WebP.');
                }
                if ($file->getSize() > 5 * 1024 * 1024) {
                    abort(422, 'Each image must be under 5MB.');
                }
            }
        }

        return $validated;
    }
}
