<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ScanTask;
use Illuminate\Http\Request;

class ScanTaskController extends Controller
{
    public function index()
    {
        return ScanTask::latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([

            'network' => 'required',
        ]);

        return ScanTask::create([

            'network' => $request->network,

            'enabled' => true,
        ]);
    }

    public function pending()
    {
        return ScanTask::where(
            'enabled',
            true
        )->get();
    }

    public function toggle($id)
    {
        $task =
            ScanTask::findOrFail($id);

        $task->enabled =
            !$task->enabled;

        $task->save();

        return $task;
    }

    public function destroy($id)
    {
        ScanTask::findOrFail($id)
            ->delete();

        return response()->json([
            'success' => true
        ]);
    }
}