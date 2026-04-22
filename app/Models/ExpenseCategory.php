<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $table = 'expense_categories';

    protected $fillable = [
        'name',
        'slug',
        'status'
    ];

    //Relation: One Category → Many Expenses
    public function expenses()
    {
        return $this->hasMany(Expenses::class, 'category_id');
    }
}
