<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntries extends Model
{
    protected $fillable = ['date', 'reference_type', 'reference_id', 'note'];

    public function items()
    {
        return $this->hasMany(JournalItems::class);
    }
}
