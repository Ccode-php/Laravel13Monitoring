<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceLog extends Model
{
    protected $fillable = [
        'device_id',
        'event_type',
        'severity',
        'old_ip',
        'new_ip',
        'old_mac',
        'new_mac',
        'message',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}