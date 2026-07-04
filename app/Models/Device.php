<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [

        'name',
    
        'ip_address',
    
        'mac_address',
    
        'status',
        'last_seen_at',
    
    ];

    
    public function logs()
    {
        return $this->hasMany(DeviceLog::class);
    }
}
