<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturn extends Model
{
    protected $fillable = [
        'user_id',
        'purchase_id',
        'supplier_id',
        'return_no',
        'return_date',
        'total_return_amount',
        'note',

        // ADD THESE
        'refunded_amount',
        'refund_due',
        'refund_status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->return_no)) {
                $model->return_no = 'PR/' . strtoupper(uniqid());
            }
        });
    }

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_return_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Customer::class, 'supplier_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
