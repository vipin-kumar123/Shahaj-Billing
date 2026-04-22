<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenses extends Model
{
    protected $fillable = [
        'expense_date',
        'category_id',
        'reference_no',
        'paid_to',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_status',
        'notes',
        'user_id'
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($expense) {
            if (empty($expense->reference_no)) {
                $lastId = Expenses::max('id') ?? 0;
                $nextId = $lastId + 1;
                $expense->reference_no = 'EXP' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Belongs to Category
    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    //Items
    public function items()
    {
        return $this->hasMany(ExpenseItems::class, 'expense_id');
    }

    //Payments
    public function payments()
    {
        return $this->hasMany(ExpensePayment::class, 'expense_id');
    }

    //Ledger
    public function ledgers()
    {
        return $this->hasMany(ExpenseLedger::class, 'expense_id');
    }
}
