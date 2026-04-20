<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierLedgers extends Model
{
    protected $fillable = [
        'user_id',
        'supplier_id',
        'purchase_id',
        'type',
        'amount',
        'note',
    ];


    // Relationships
    public function supplier()
    {
        return $this->belongsTo(Customer::class, 'supplier_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
