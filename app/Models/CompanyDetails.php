<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyDetails extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'email',
        'mobile',
        'tax_number',
        'address',
        'state_id',
        'city_id',
        'company_logo',
        'ip'
    ];

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(Cities::class, 'city_id');
    }
}
