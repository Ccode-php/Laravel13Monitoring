<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function index()
    {
        return Device::with('latestLog')
            ->latest()
            ->paginate(50);
    }

    public function show($id)
    {
        return Device::with([
            'logs' => function ($query) {
                $query->latest();
            }
        ])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $device = Device::findOrFail($id);

        $request->validate([

            'name' => 'required|max:255'

        ]);

        $device->update([

            'name' => $request->name

        ]);

        return response()->json([
            'success' => true,
            'device' => $device
        ]);
    }
}
