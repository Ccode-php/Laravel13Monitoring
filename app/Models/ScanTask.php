<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanTask extends Model
{
    protected $fillable = [

        'network',

        'enabled',

    ];

    protected $casts = [

        'enabled' => 'boolean',

    ];
}