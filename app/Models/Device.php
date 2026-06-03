<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'ip_address',
        'mac_address',
        'hostname',
        'vendor',
        'device_type',
        'system_name',
        'system_description',
        'snmp_enabled',
        'snmp_version',
        'snmp_community',
        'status',
        'first_seen_at',
        'last_seen_at',
        'extra_data',
        'last_event',
        'last_event_message',
        'last_event_at',
    ];

    protected $casts = [
        'extra_data' => 'array',

        'snmp_enabled' => 'boolean',

        'first_seen_at' => 'datetime',

        'last_seen_at' => 'datetime',
        'last_event_at' => 'datetime',
    ];

    public function logs()
    {
        return $this->hasMany(DeviceLog::class);
    }

    public function metrics()
    {
        return $this->hasMany(DeviceMetric::class);
    }
}
