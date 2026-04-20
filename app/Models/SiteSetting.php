<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'user_id',
        'app_name',
        'footer_text',
        'language',
        'timezone',
        'date_format',
        'time_format',
        'logo',
        'favicon',
    ];
}
