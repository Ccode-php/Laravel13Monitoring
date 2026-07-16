<?php

namespace App\Services;

use App\Models\NetworkSwitch;
use App\Models\Device;

class TopologyService
{
    public function build()
    {
        $result = [];

        $switches = NetworkSwitch::with('ports')->get();

        foreach ($switches as $switch) {

            $node = [

                'id' => $switch->id,

                'hostname' => $switch->hostname,

                'ip' => $switch->ip_address,

                'mac' => $switch->mac_address,

                'ports' => [],

            ];

            foreach ($switch->ports as $port) {

                $devices = Device::whereIn(

                    'mac_address',

                    $port->connected_macs ?? []

                )->get();

                $children = [];

                foreach ($devices as $device) {

                    $children[] = [

                        'id' => $device->id,

                        'name' => $device->name,

                        'ip' => $device->ip_address,

                        'mac' => $device->mac_address,

                        'status' => $device->status,

                    ];

                }

                $node['ports'][] = [

                    'port' => $port->port_name,

                    'devices' => $children,

                ];

            }

            $result[] = $node;

        }

        return $result;
    }
}