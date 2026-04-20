<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $fillable = [
        'purchase_return_id',
        'purchase_item_id',
        'purchase_id',
        'product_id',
        'unit_cost',
        'return_qty',
        'gst_percent',
        'gst_amount',
        'total'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
