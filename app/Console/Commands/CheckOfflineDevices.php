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
        $devices = Device::where('status', 'ONLINE')
            ->whereNotNull('last_seen_at')
            ->where('last_seen_at', '<', now()->subMinutes(2))
            ->get();

        foreach ($devices as $device) {

            $device->update([

                'status' =>
                'OFFLINE',
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
