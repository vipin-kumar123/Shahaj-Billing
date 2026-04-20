<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalItems extends Model
{
    protected $fillable = ['journal_entry_id', 'account_id', 'debit', 'credit'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
