<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseAttachment extends Model
{
    protected $fillable = [
        'expense_id',
        'file_path'
    ];

    public function expense()
    {
        return $this->belongsTo(Expenses::class, 'expense_id');
    }
}
