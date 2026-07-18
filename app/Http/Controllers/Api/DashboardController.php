<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\NetworkSwitch;
use App\Models\ScanTask;
use App\Services\TopologyService;

class DashboardController extends Controller
{
    public function index(TopologyService $topology)
    {
        return response()->json([

            /*
            |--------------------------------------------------------------------------
            | Statistika
            |--------------------------------------------------------------------------
            */

            'stats' => [

                'totalDevices' => Device::count(),

                'onlineDevices' => Device::where(
                    'status',
                    'ONLINE'
                )->count(),

                'offlineDevices' => Device::where(
                    'status',
                    'OFFLINE'
                )->count(),

                'totalNetworks' => ScanTask::count(),

                'activeNetworks' => ScanTask::where(
                    'enabled',
                    true
                )->count(),

            ],


            /*
            |--------------------------------------------------------------------------
            | Oxirgi hodisalar
            |--------------------------------------------------------------------------
            */

            'logs' => DeviceLog::with('device')
                ->latest()
                ->limit(20)
                ->get(),

        ]);
    }
}