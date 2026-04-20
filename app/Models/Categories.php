<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'is_active',
        'ip'
    ];
}
