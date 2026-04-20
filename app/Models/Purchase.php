<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'supplier_id',
        'purchase_date',
        'bill_no',
        'reference_no',
        'payment_status',
        'subtotal',
        'discount_type',
        'discount_amount',
        'tax_amount',
        'shipping_charges',
        'rounding',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_method',
        'notes',
        'attachment',
        'created_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (empty($purchase->reference_no)) {
                $purchase->reference_no = 'PB/' . strtoupper(uniqid());
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Customer::class, 'supplier_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class, 'purchase_id');
    }

    public function payments()
    {
        return $this->hasMany(PartyTransaction::class, 'reference_id');
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class, 'purchase_id');
    }
}
