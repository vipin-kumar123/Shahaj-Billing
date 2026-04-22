<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseItems extends Model
{
    protected $fillable = [
        'expense_id',
        'description',
        'category_id',
        'amount',
        'tax_amount',
        'total'
    ];

    public function expense()
    {
        return $this->belongsTo(Expenses::class, 'expense_id');
    }
}
