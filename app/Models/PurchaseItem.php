<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id',
        'product_id',
        'unit_id',
        'unit_cost',
        'quantity',
        'discount',
        'discount_type',
        'gst_percent',
        'gst_amount',
        'total',
        'expiry_date',
        'batch_no',
    ];

    // Relationships
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
