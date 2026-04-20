<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'description',
        'is_active',
        'ip'
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }
}
