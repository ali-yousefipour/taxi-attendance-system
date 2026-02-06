<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDeviceController extends Controller
{
    public function store(Request $request)
    {
        $request->validate(['device_token'=>'required|string']);

        $device = UserDevice::updateOrCreate(
            ['user_id'=>Auth::id()],
            ['device_token'=>$request->device_token]
        );

        return response()->json(['message'=>'Device token registered','device'=>$device]);
    }
}
