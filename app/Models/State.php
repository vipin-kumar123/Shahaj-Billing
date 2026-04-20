<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = ['name', 'code', 'is_active'];

    public function cities()
    {
        return $this->hasMany(Cities::class);
    }
}
