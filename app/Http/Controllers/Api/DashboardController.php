<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\ScanTask;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->json([

            'stats' => [

                'total' => Device::count(),

                'online' => Device::where(
                    'status',
                    'ONLINE'
                )->count(),

                'offline' => Device::where(
                    'status',
                    'OFFLINE'
                )->count(),

                'totalNetworks' => ScanTask::count(),

                'activeNetworks' => ScanTask::where(
                    'enabled',
                    true
                )->count(),

            ],

            'devices' => Device::select(
                'id',
                'name',
                'ip_address',
                'status'
            )->get(),

            'logs' => DeviceLog::with('device')
                ->latest()
                ->limit(10)
                ->get(),

        ]);
    }
}