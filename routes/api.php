use App\Http\Controllers\Api\ReportApiController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/reports', [ReportApiController::class,'index']);          // لیست گزارش‌ها
    Route::get('/reports/{report}', [ReportApiController::class,'show']); // جزئیات گزارش
    Route::post('/reports', [ReportApiController::class,'store']);        // ثبت گزارش جدید
    Route::post('/reports/{report}/note', [ReportApiController::class,'addNote']); // اضافه کردن یادداشت
    Route::post('/reports/{report}/approve', [ReportApiController::class,'approve']); // تایید
    Route::post('/reports/{report}/reject', [ReportApiController::class,'reject']);   // رد کردن
});
