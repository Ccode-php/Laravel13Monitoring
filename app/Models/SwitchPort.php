<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwitchPort extends Model
{
    protected $fillable = [

        'network_switch_id',

        'port_index',

        'port_name',

        'connected_macs',

    ];

    protected $casts = [

        'connected_macs' => 'array',

    ];

    public function networkSwitch()
    {
        return $this->belongsTo(
            NetworkSwitch::class
        );
    }

    public function devices()
    {
        return $this->hasMany(
            Device::class
        );
    }
}