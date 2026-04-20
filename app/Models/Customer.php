<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'customer_type',
        'first_name',
        'last_name',
        'email',
        'mobile_number',
        'alternate_number',
        'whatsapp_number',
        'village',
        'mohalla',
        'district',
        'area',
        'city',
        'state',
        'pincode',
        'billing_address',
        'shipping_address',
        'business_name',
        'gst_number',
        'pan_number',
        'opening_balance',
        'udhar_limit',
        'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cityData()
    {
        return $this->belongsTo(Cities::class, 'city');
    }

    public function stateData()
    {
        return $this->belongsTo(State::class, 'state');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'supplier_id');
    }

    public function ledgers()
    {
        return $this->hasMany(SupplierLedgers::class, 'supplier_id');
    }

    public function saleLedgers()
    {
        return $this->hasMany(CustomerLedger::class, 'customer_id');
    }

    public function saleReturns()
    {
        return $this->hasMany(SaleReturn::class, 'customer_id');
    }
}
