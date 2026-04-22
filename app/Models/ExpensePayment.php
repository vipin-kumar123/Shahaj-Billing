<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpensePayment extends Model
{
    protected $fillable = [
        'expense_id',
        'payment_date',
        'reference_no',
        'amount',
        'payment_method',
        'note'
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expayment) {
            if (empty($expayment->reference_no)) {
                $expayment->reference_no = 'PAY' . time();
            }
        });
    }

    public function expense()
    {
        return $this->belongsTo(Expenses::class, 'expense_id');
    }
}
