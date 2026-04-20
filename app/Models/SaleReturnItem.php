<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturnItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_return_id',
        'sale_id',
        'sale_item_id',
        'product_id',
        'unit_price',
        'return_qty',
        'gst_percent',
        'gst_amount',
        'total'
    ];


    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }


    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
