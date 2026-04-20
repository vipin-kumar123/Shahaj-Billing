<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'brand_id',
        'product_code',
        'barcode_code',
        'hsn_code',
        'sku',
        'name',
        'slug',
        'unit',
        'purchase_price',
        'distributor_price',
        'wholesale_price',
        'saleing_price',
        'gst_percentage',
        'opening_stock',
        'low_stock_alert',
        'product_type',
        'status',
        'user_id',
        'ip',
    ];

    // Auto-generate codes on create
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {

            // Auto product code → PRD0001, PRD0002...
            $lastId = Product::max('id') ?? 0;
            $product->product_code = 'PRD' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

            // Auto barcode → 13-digit EAN style
            $product->barcode_code = '890' . rand(1000000000, 9999999999);

            // Auto slug
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }

            // AUTO SKU ONLY IF PRODUCT TYPE = VARIANT
            if ($product->product_type === 'variant') {

                if (empty($product->sku)) {
                    $lastId = Product::max('id') + 1;
                    $product->sku = 'SKU' . str_pad($lastId, 5, '0', STR_PAD_LEFT);
                }
            }

            // When updating an existing product
            static::updating(function ($product) {
                // Only update slug if product name changed
                if ($product->isDirty('name')) {
                    $product->slug = Str::slug($product->name);
                }
            });
        });
    }

    // Relationships
    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
