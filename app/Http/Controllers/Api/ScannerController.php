<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceLog;
use Illuminate\Http\Request;

class ScannerController extends Controller
{
    public function reportBatch(Request $request)
    {
        if ($request->header('X-SCANNER-TOKEN') !== env('SCANNER_TOKEN')) {

            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        foreach ($request->all() as $item) {

            if (
                empty($item['ip']) ||
                empty($item['mac'])
            ) {
                continue;
            }

            $ip = $item['ip'];
            $mac = strtoupper($item['mac']);

            /*
            |--------------------------------------------------------------------------
            | MAC bo'yicha qidiramiz
            |--------------------------------------------------------------------------
            */

            $device = Device::where(
                'mac_address',
                $mac
            )->first();

            /*
            |--------------------------------------------------------------------------
            | Qurilma mavjud emas
            |--------------------------------------------------------------------------
            */

            if (!$device) {

                /*
                |--------------------------------------------------------------------------
                | Shu IP oldin boshqa MAC bilan ishlatilganmi?
                |--------------------------------------------------------------------------
                */

                $sameIp = Device::where(
                    'ip_address',
                    $ip
                )->first();

                if ($sameIp) {

                    DeviceLog::create([
                        'device_id'  => $sameIp->id,
                        'event_type' => 'MAC_CHANGED',
                        'severity'   => 'WARNING',
                        'old_mac'    => $sameIp->mac_address,
                        'new_mac'    => $mac,
                        'message'    => 'MAC manzili o‘zgardi.',
                    ]);

                    $sameIp->update([
                        'mac_address'  => $mac,
                        'status'       => 'ONLINE',
                        'last_seen_at' => now(),
                    ]);

                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Yangi qurilma
                |--------------------------------------------------------------------------
                */

                $device = Device::create([
                    'ip_address'   => $ip,
                    'mac_address'  => $mac,
                    'status'       => 'ONLINE',
                    'last_seen_at' => now(),
                ]);

                DeviceLog::create([
                    'device_id'  => $device->id,
                    'event_type' => 'NEW_DEVICE',
                    'severity'   => 'INFO',
                    'new_ip'     => $ip,
                    'new_mac'    => $mac,
                    'message'    => 'Yangi qurilma aniqlandi.',
                ]);

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | IP o'zgarganmi?
            |--------------------------------------------------------------------------
            */

            if ($device->ip_address != $ip) {

                DeviceLog::create([
                    'device_id'  => $device->id,
                    'event_type' => 'IP_CHANGED',
                    'severity'   => 'WARNING',
                    'old_ip'     => $device->ip_address,
                    'new_ip'     => $ip,
                    'message'    => 'IP manzili o‘zgardi.',
                ]);

                $device->ip_address = $ip;
            }

            /*
            |--------------------------------------------------------------------------
            | Qurilma online
            |--------------------------------------------------------------------------
            */

            $device->status = 'ONLINE';
            $device->last_seen_at = now();

            $device->save();
        }

        return response()->json([
            'success' => true
        ]);
    }
}
