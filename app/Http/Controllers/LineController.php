<?php

namespace App\Http\Controllers;

use App\Models\Line;
use App\Models\Driver;
use App\Models\DriverWarning;
use App\Models\LineVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DriversImport;

class LineController extends Controller
{
    // لیست خطوط
    public function index()
    {
        $lines = Line::with('drivers')->get();
        return view('lines.index', compact('lines'));
    }

    // مشاهده جزئیات خط و رانندگان
    public function show(Line $line)
    {
        $drivers = $line->drivers()->with('warnings','attendances')->get();
        return view('lines.show', compact('line','drivers'));
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

    // گزارش عملکرد خط
    public function performance(Line $line)
    {
        $drivers = $line->drivers()->with(['attendances','warnings'])->get();

        $report = $drivers->map(function($driver){
            $totalDebt = $driver->warnings->sum('debt');
            $daysPresent = $driver->attendances->whereNotNull('check_in')->count();
            return [
                'driver_name'=>$driver->name,
                'national_id'=>$driver->national_id,
                'days_present'=>$daysPresent,
                'total_debt'=>$totalDebt
            ];
        });

        return view('lines.performance', compact('line','report'));
    }

    // ثبت بازدید مسئولین
    public function addVisit(Request $request, Line $line)
    {
        $request->validate([
            'visit_date'=>'required|date',
            'notes'=>'nullable|string',
            'photo'=>'nullable|image|max:2048'
        ]);

        $data = $request->only('visit_date','notes');
        $data['line_id'] = $line->id;
        $data['visited_by'] = Auth::id();

        if($request->hasFile('photo')){
            $data['photo'] = $request->photo->store('visits','public');
        }

        LineVisit::create($data);

        return back()->with('success','بازدید ثبت شد');
    }

    // ایمپورت رانندگان از Excel
    public function importDrivers(Request $request)
    {
        $request->validate([
            'file'=>'required|mimes:xlsx,xls'
        ]);

        Excel::import(new DriversImport, $request->file('file'));

        return back()->with('success','رانندگان با موفقیت وارد شدند');
    }
}
