<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sales extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'user_id',
        'customer_id',
        'sale_date',
        'invoice_no',
        'reference_no',
        'payment_status',
        'subtotal',
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

    protected $casts = [
        'sale_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_charges' => 'decimal:2',
        'rounding' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($purchase) {
            if (empty($purchase->reference_no)) {
                $purchase->reference_no = 'SL/' . strtoupper(uniqid());
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    public function payments()
    {
        return $this->hasMany(PartyTransaction::class, 'reference_id');
    }
}
