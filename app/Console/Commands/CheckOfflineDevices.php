<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device;
use App\Models\DeviceLog;

class CheckOfflineDevices extends Command
{
    protected $signature =
        'devices:check-offline';

    protected $description =
        'Offline qurilmalarni tekshiradi';

    public function handle()
    {
        $devices = Device::where(
            'status',
            'ONLINE'
        )
        ->where(
            'last_seen_at',
            '<',
            now()->subMinutes(2)
        )
        ->get();

        foreach ($devices as $device) {

            $device->update([

                'status' =>
                    'OFFLINE',

                'last_event' =>
                    'QURILMA OFFLINE',

                'last_event_message' =>
                    'Qurilma tarmoqdan uzildi',

                'last_event_at' =>
                    now(),
            ]);

            DeviceLog::create([

                'device_id' =>
                    $device->id,

                'event_type' =>
                    'DEVICE_OFFLINE',

                'severity' =>
                    'CRITICAL',

                'message' =>
                    'Qurilma offline bo‘ldi',
            ]);
        }

        return Command::SUCCESS;
    }
}