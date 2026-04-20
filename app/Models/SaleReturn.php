<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sale_id',
        'customer_id',
        'return_no',
        'return_date',
        'total_return_amount',
        'note',
        'refunded_amount',
        'refund_due',
        'refund_status'
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->return_no)) {
                $model->return_no = 'SR/' . strtoupper(uniqid());
            }
        });
    }

    public function sale()
    {
        return $this->belongsTo(Sales::class);
    }


    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }


    public function items()
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
