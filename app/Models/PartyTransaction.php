<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartyTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'party_id',
        'reference_type',
        'reference_id',
        'type',
        'amount',
        'payment_method',
        'payment_date',
        'note',
    ];

    public function party()
    {
        return $this->belongsTo(Customer::class, 'party_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
