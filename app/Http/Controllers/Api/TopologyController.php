<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TopologyService;

class TopologyController extends Controller
{
    public function index(TopologyService $topology)
    {
        return response()->json(

            $topology->build()

        );
    }
}