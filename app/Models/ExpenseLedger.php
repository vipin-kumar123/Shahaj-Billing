<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseLedger extends Model
{
    protected $fillable = [
        'expense_id',
        'user_id',
        'type',
        'amount',
        'note'
    ];

    public function expense()
    {
        return $this->belongsTo(Expenses::class, 'expense_id');
    }
}
