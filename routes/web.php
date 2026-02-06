<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserDeviceController;

// صفحه اصلی — هدایت به لیست گزارش‌ها
Route::get('/', function(){
    return redirect()->route('reports.index');
});

// احراز هویت
Auth::routes();

// همه Routeهای سیستم گزارش روزانه تحت Middleware احراز هویت
Route::middleware(['auth'])->group(function(){

    // داشبورد مدیریتی
    Route::get('/dashboard', [ReportController::class,'dashboard'])->name('dashboard');

    // لیست گزارش‌ها
    Route::get('/reports', [ReportController::class,'index'])->name('reports.index');

    // فرم ثبت گزارش جدید
    Route::get('/reports/create', [ReportController::class,'create'])->name('reports.create');

    // ذخیره گزارش جدید
    Route::post('/reports', [ReportController::class,'store'])->name('reports.store');

    // مشاهده جزئیات گزارش و Workflow
    Route::get('/reports/{report}', [ReportController::class,'show'])->name('reports.show');

    // اضافه کردن یادداشت به گزارش
    Route::post('/reports/{report}/note', [ReportController::class,'addNote'])->name('reports.note');

    // تایید گزارش
    Route::post('/reports/{report}/approve', [ReportController::class,'approve'])->name('reports.approve');

    // رد گزارش
    Route::post('/reports/{report}/reject', [ReportController::class,'reject'])->name('reports.reject');

    // ثبت توکن دستگاه کاربر برای نوتیفیکیشن
    Route::post('/devices/register', [UserDeviceController::class,'store'])->name('devices.register');
});
