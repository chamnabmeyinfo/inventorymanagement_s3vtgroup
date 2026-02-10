<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    protected $fillable = [
        'sku', 'name', 'slug', 'category_id', 'description', 'specifications',
        'price_display_type', 'price_amount', 'image_urls', 'related_product_ids',
    ];

    protected $casts = [
        'specifications' => 'array',
        'image_urls' => 'array',
        'related_product_ids' => 'array',
        'price_amount' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
