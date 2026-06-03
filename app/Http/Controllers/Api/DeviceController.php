<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        return Device::latest()->paginate(50);
    }

    public function show($id)
    {
        return Device::with([

            'logs' => function ($query) {

                $query->latest();
            },

            'metrics' => function ($query) {

                $query->latest()->limit(20);
            },

        ])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $device = Device::findOrFail($id);

        $request->validate([

            'hostname' => 'nullable|string|max:255',

            'vendor' => 'nullable|string|max:255',
        ]);

        $device->update([

            'hostname' =>
            $request->hostname,

            'vendor' =>
            $request->vendor,
        ]);

        return response()->json([
            'success' => true,
            'device' => $device
        ]);
    }
}
