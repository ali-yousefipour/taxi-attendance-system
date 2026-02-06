<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Line;
use App\Models\DriverRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    // جستجوی راننده با محدودیت خط
    public function search(Request $request)
    {
        $query = $request->input('q');
        $user = Auth::user();

        // رانندگان خطوطی که کاربر به آنها دسترسی دارد
        $accessibleLineIds = $user->lines->pluck('id')->toArray();

        $drivers = Driver::whereIn('line_id', $accessibleLineIds)
            ->where(function($q) use ($query){
                $q->where('national_id', 'like', "%$query%")
                  ->orWhere('name', 'like', "%$query%");
            })
            ->limit(10)
            ->get();

        return response()->json($drivers);
    }

    // ثبت راننده جدید و ایجاد درخواست تایید برای نیروی اداری
    public function createRequest(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'national_id'=>'required|string|max:20',
            'line_id'=>'required|exists:lines,id'
        ]);

        $user = Auth::user();

        // بررسی دسترسی به خط
        if(!$user->lines->contains($request->line_id)){
            return response()->json(['error'=>'دسترسی به این خط ندارید'],403);
        }

        // ایجاد درخواست راننده جدید
        $driverRequest = DriverRequest::create([
            'line_id' => $request->line_id,
            'name' => $request->name,
            'national_id' => $request->national_id,
            'requested_by' => $user->id,
            'status' => 'pending'
        ]);

        return response()->json([
            'message'=>'درخواست ثبت شد و منتظر تایید نیروی اداری است',
            'request_id'=>$driverRequest->id
        ]);
    }

    // تایید درخواست توسط نیروی اداری
    public function approveRequest(DriverRequest $requestObj)
    {
        $requestObj->update(['status'=>'approved']);

        // اضافه کردن راننده به خط
        $driver = Driver::create([
            'line_id'=>$requestObj->line_id,
            'name'=>$requestObj->name,
            'national_id'=>$requestObj->national_id
        ]);

        return response()->json([
            'message'=>'راننده اضافه شد',
            'driver'=>$driver
        ]);
    }

    // رد درخواست توسط نیروی اداری
    public function rejectRequest(DriverRequest $requestObj)
    {
        $requestObj->update(['status'=>'rejected']);
        return response()->json(['message'=>'درخواست رد شد']);
    }
}
// ثبت تذکر و بدهی راننده
public function addWarning(Request $request, Driver $driver)
{
    $request->validate([
        'warning'=>'required|string',
        'debt'=>'nullable|numeric'
    ]);

    DriverWarning::create([
        'driver_id'=>$driver->id,
        'added_by'=>Auth::id(),
        'warning'=>$request->warning,
        'debt'=>$request->debt ?? 0
    ]);

    // به‌روز رسانی بدهی راننده
    if($request->debt){
        $driver->increment('balance', $request->debt);
    }

    return back()->with('success','تذکر ثبت شد');
}
