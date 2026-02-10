<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = 'product_id';

    protected $fillable = ['product_id', 'quantity', 'status'];

    protected function casts(): array
    {
        return [
            'updated_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
