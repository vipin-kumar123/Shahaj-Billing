<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    protected $fillable = ['state_id', 'name', 'is_active'];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
