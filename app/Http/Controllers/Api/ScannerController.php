<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\NetworkSwitch;
use App\Models\SwitchPort;

class ScannerController extends Controller
{
    public function reportBatch(Request $request)
    {
        if (
            $request->header('X-SCANNER-TOKEN')
            !== env('SCANNER_TOKEN')
        ) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $devices = $request->input('devices', []);

        $switches = $request->input('switches');

        if (!is_array($switches)) {
            $switches = [];
        }

        /*
        |--------------------------------------------------------------------------
        | DEVICE LAR
        |--------------------------------------------------------------------------
        */

        foreach ($devices as $item) {

            if (
                empty($item['ip']) ||
                empty($item['mac'])
            ) {
                continue;
            }

            $ip = $item['ip'];

            $mac = strtoupper($item['mac']);

            $hostname =
                $item['hostname'] ?? null;

            $device = Device::where(
                'mac_address',
                $mac
            )->first();

            /*
            |--------------------------------------------------------------------------
            | YANGI DEVICE
            |--------------------------------------------------------------------------
            */

            if (!$device) {

                $sameIp = Device::where(
                    'ip_address',
                    $ip
                )->first();

                if ($sameIp) {

                    DeviceLog::create([

                        'device_id' => $sameIp->id,

                        'event_type' => 'MAC_CHANGED',

                        'severity' => 'WARNING',

                        'old_mac' => $sameIp->mac_address,

                        'new_mac' => $mac,

                        'message' => 'MAC manzili o‘zgardi.',

                    ]);

                    $sameIp->update([

                        'mac_address' => $mac,

                        'status' => 'ONLINE',

                        'last_seen_at' => now(),

                    ]);

                    continue;
                }

                $device = Device::create([

                    'name' => $hostname,

                    'ip_address' => $ip,

                    'mac_address' => $mac,

                    'status' => 'ONLINE',

                    'last_seen_at' => now(),

                ]);

                DeviceLog::create([

                    'device_id' => $device->id,

                    'event_type' => 'NEW_DEVICE',

                    'severity' => 'INFO',

                    'new_ip' => $ip,

                    'new_mac' => $mac,

                    'message' => 'Yangi qurilma topildi.',

                ]);

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | IP O'ZGARGAN
            |--------------------------------------------------------------------------
            */

            if ($device->ip_address != $ip) {

                DeviceLog::create([

                    'device_id' => $device->id,

                    'event_type' => 'IP_CHANGED',

                    'severity' => 'WARNING',

                    'old_ip' => $device->ip_address,

                    'new_ip' => $ip,

                    'message' => 'IP manzili o‘zgardi.',

                ]);

                $device->ip_address = $ip;
            }

            /*
            |--------------------------------------------------------------------------
            | HOSTNAME
            |--------------------------------------------------------------------------
            */

            if (
                empty($device->name) &&
                !empty($hostname)
            ) {
                $device->name = $hostname;
            }

            $device->status = 'ONLINE';

            $device->last_seen_at = now();

            $device->save();
        }

        /*
        |--------------------------------------------------------------------------
        | 2-QISM
        | SWITCH LAR
        |--------------------------------------------------------------------------
        */

        foreach ($switches as $item) {

            if (
                empty($item['ip']) ||
                empty($item['mac'])
            ) {
                continue;
            }

            $ip = $item['ip'];

            $mac = strtoupper($item['mac']);

            $hostname =
                $item['hostname'] ?? null;

            /*
            |--------------------------------------------------------------------------
            | SWITCH
            |--------------------------------------------------------------------------
            */

            $switch = NetworkSwitch::updateOrCreate(

                [
                    'mac_address' => $mac,
                ],

                [
                    'ip_address' => $ip,

                    'hostname' => $hostname,
                ]

            );

            /*
            |--------------------------------------------------------------------------
            | PORTLAR
            |--------------------------------------------------------------------------
            */

            foreach ($item['ports'] ?? [] as $port) {

                $macs = [];

                foreach ($port['macs'] ?? [] as $m) {

                    $macs[] = strtoupper($m);
                }

                SwitchPort::updateOrCreate(

                    [

                        'network_switch_id' => $switch->id,

                        'port_index' => $port['index'],

                    ],

                    [

                        'port_name' => $port['name'],

                        'connected_macs' => $macs,

                    ]

                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 3-QISM
        | DEVICE ↔ SWITCH PORT
        |--------------------------------------------------------------------------
        */

        foreach (SwitchPort::all() as $port) {

            foreach ($port->connected_macs ?? [] as $mac) {

                $device = Device::where(
                    'mac_address',
                    strtoupper($mac)
                )->first();

                if (!$device) {
                    continue;
                }

                $device->switch_port_id = $port->id;

                $device->save();
            }
        }

        return response()->json([

            'success' => true,

        ]);
    }
}
