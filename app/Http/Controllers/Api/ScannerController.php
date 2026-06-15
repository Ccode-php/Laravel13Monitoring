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
        $token = $request->header('X-SCANNER-TOKEN');

        if ($token !== env('SCANNER_TOKEN')) {

            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $devices = $request->all();

        foreach ($devices as $item) {

            if (
                empty($item['ip']) ||
                empty($item['mac'])
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | FIND DEVICE BY MAC
            |--------------------------------------------------------------------------
            */

            $device = Device::where(
                'mac_address',
                $item['mac']
            )->first();

            /*
            |--------------------------------------------------------------------------
            | MAC CHANGED
            |--------------------------------------------------------------------------
            */

            $sameIpDevice = Device::where(
                'ip_address',
                $item['ip']
            )
            ->where(
                'mac_address',
                '!=',
                $item['mac']
            )
            ->first();

            if (
                !$device &&
                $sameIpDevice
            ) {

                $oldMac =
                    $sameIpDevice->mac_address;

                DeviceLog::create([

                    'device_id' =>
                        $sameIpDevice->id,

                    'event_type' =>
                        'MAC_CHANGED',

                    'severity' =>
                        'CRITICAL',

                    'old_mac' =>
                        $oldMac,

                    'new_mac' =>
                        $item['mac'],

                    'message' =>
                        'MAC manzil o‘zgardi',
                ]);

                $sameIpDevice->update([

                    'mac_address' =>
                        $item['mac'],

                    'hostname' =>
                        $item['hostname'] ?? null,

                    'vendor' =>
                        $item['vendor'] ?? null,

                    'device_type' =>
                        $item['device_type'] ?? null,

                    'system_name' =>
                        $item['system_name'] ?? null,

                    'system_description' =>
                        $item['system_description'] ?? null,

                    'snmp_enabled' =>
                        $item['snmp_enabled'] ?? false,

                    'snmp_version' =>
                        $item['snmp_version'] ?? null,

                    'status' =>
                        'ONLINE',

                    'last_seen_at' =>
                        now(),

                    'last_scan_at' =>
                        now(),

                    'last_event' =>
                        'MAC O‘ZGARDI',

                    'last_event_message' =>
                        'MAC manzil ' .
                        $oldMac .
                        ' dan ' .
                        $item['mac'] .
                        ' ga o‘zgardi',

                    'last_event_at' =>
                        now(),
                ]);

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | NEW DEVICE
            |--------------------------------------------------------------------------
            */

            if (!$device) {

                $newDevice = Device::create([
                    'ip_address' => $item['ip'],
                    'mac_address' => $item['mac'],
                    'hostname' => $item['hostname'] ?? null,
                    'vendor' => $item['vendor'] ?? null,
                    'device_type' => $item['device_type'] ?? null,
                    'system_name' => $item['system_name'] ?? null,
                    'system_description' => $item['system_description'] ?? null,
                    'snmp_enabled' => $item['snmp_enabled'] ?? false,
                    'snmp_version' => $item['snmp_version'] ?? null,
                    'status' => 'ONLINE',
                
                    'first_seen_at' => now(),
                    'last_seen_at' => now(),
                    'last_scan_at' => now(),
                
                    'last_event' => 'YANGI QURILMA',
                    'last_event_message' => 'Yangi qurilma aniqlandi',
                    'last_event_at' => now(),
                ]);

                DeviceLog::create([

                    'device_id' =>
                        $newDevice->id,

                    'event_type' =>
                        'NEW_DEVICE',

                    'severity' =>
                        'INFO',

                    'new_ip' =>
                        $item['ip'],

                    'new_mac' =>
                        $item['mac'],

                    'message' =>
                        'Yangi qurilma aniqlandi',
                ]);

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | IP CHANGED
            |--------------------------------------------------------------------------
            */

            if (
                $device->ip_address !==
                $item['ip']
            ) {

                $oldIp =
                    $device->ip_address;

                DeviceLog::create([

                    'device_id' =>
                        $device->id,

                    'event_type' =>
                        'IP_CHANGED',

                    'severity' =>
                        'WARNING',

                    'old_ip' =>
                        $oldIp,

                    'new_ip' =>
                        $item['ip'],

                    'message' =>
                        'IP manzil o‘zgardi',
                ]);

                $device->last_event =
                    'IP O‘ZGARDI';

                $device->last_event_message =
                    'IP manzil ' .
                    $oldIp .
                    ' dan ' .
                    $item['ip'] .
                    ' ga o‘zgardi';

                $device->last_event_at =
                    now();
            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE DEVICE
            |--------------------------------------------------------------------------
            */

            $device->ip_address =
                $item['ip'];

            $device->hostname =
                $item['hostname'] ?? null;

            $device->vendor =
                $item['vendor'] ?? null;

            $device->device_type =
                $item['device_type'] ?? null;

            $device->system_name =
                $item['system_name'] ?? null;

            $device->system_description =
                $item['system_description'] ?? null;

            $device->snmp_enabled =
                $item['snmp_enabled'] ?? false;

            $device->snmp_version =
                $item['snmp_version'] ?? null;

            $device->status =
                'ONLINE';

            $device->last_seen_at =
                now();

            $device->last_scan_at =
                now();

            $device->save();
        }

        return response()->json([
            'success' => true
        ]);
    }
}