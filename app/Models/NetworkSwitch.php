<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NetworkSwitch extends Model
{
    protected $fillable = [

        'hostname',

        'ip_address',

        'mac_address',

        'vendor',

        'model',

    ];

    public function ports()
    {
        return $this->hasMany(
            SwitchPort::class
        );
    }
}