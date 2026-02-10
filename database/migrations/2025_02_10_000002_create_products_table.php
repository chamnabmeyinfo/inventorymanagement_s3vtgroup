<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 64);
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->enum('price_display_type', ['on_request', 'fixed'])->default('on_request');
            $table->decimal('price_amount', 12, 2)->nullable();
            $table->json('image_urls')->nullable();
            $table->json('related_product_ids')->nullable();
            $table->timestamps();

            $table->unique('sku');
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
