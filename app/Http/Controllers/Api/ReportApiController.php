<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportNote;
use App\Models\ReportStatusHistory;
use App\Models\UserDevice;
use App\Notifications\ReportNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportApiController extends Controller
{
    // لیست گزارش‌ها (گزارش‌های خود + زیرمجموعه‌ها)
    public function index()
    {
        $user = Auth::user();

        $reports = Report::with('user','reviewer','notes.user','statusHistory')
            ->where('user_id', $user->id)
            ->orWhereHas('user', function($q) use($user){
                $q->where('parent_id', $user->id);
            })
            ->latest()
            ->get();

        return response()->json($reports);
    }

    // مشاهده جزئیات گزارش
    public function show(Report $report)
    {
        $report->load('user','reviewer','notes.user','statusHistory');
        return response()->json($report);
    }

    // ثبت گزارش جدید
    public function store(Request $request)
    {
        $request->validate([
            'title'=>'required|string|max:255',
            'description'=>'required|string',
        ]);

        $user = Auth::user();

        $report = Report::create([
            'user_id' => $user->id,
            'current_reviewer_id' => $user->parent_id,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'submitted'
        ]);

        $this->logStatus($report,'draft','submitted');
        $this->notifyReviewer($report);

        return response()->json([
            'message'=>'گزارش با موفقیت ثبت شد',
            'report'=>$report
        ]);
    }

    // اضافه کردن یادداشت
    public function addNote(Request $request, Report $report)
    {
        $request->validate([
            'note'=>'required|string'
        ]);

        $note = ReportNote::create([
            'report_id'=>$report->id,
            'user_id'=>Auth::id(),
            'note'=>$request->note
        ]);

        return response()->json([
            'message'=>'یادداشت اضافه شد',
            'note'=>$note
        ]);
    }

    // تایید گزارش
    public function approve(Report $report)
    {
        $old = $report->status;

        $nextReviewer = $report->reviewer?->parent_id;

        $report->update([
            'status' => $nextReviewer ? 'in_review' : 'approved',
            'current_reviewer_id' => $nextReviewer
        ]);

        $this->logStatus($report,$old,$report->status);
        $this->notifyUser($report,$report->status=='approved' ? "گزارش شما تایید شد" : "گزارش شما در حال بررسی است");

        return response()->json([
            'message'=>'گزارش تایید شد',
            'report'=>$report
        ]);
    }

    // رد گزارش
    public function reject(Report $report)
    {
        $old = $report->status;

        $report->update([
            'status'=>'rejected',
            'current_reviewer_id'=>null
        ]);

        $this->logStatus($report,$old,'rejected');
        $this->notifyUser($report,"گزارش شما رد شد");

        return response()->json([
            'message'=>'گزارش رد شد',
            'report'=>$report
        ]);
    }

    // ثبت تاریخچه وضعیت
    private function logStatus($report,$old,$new)
    {
        ReportStatusHistory::create([
            'report_id'=>$report->id,
            'changed_by'=>Auth::id(),
            'old_status'=>$old,
            'new_status'=>$new
        ]);
    }

    // نوتیفیکیشن برای مسئول بررسی
    private function notifyReviewer($report)
    {
        if(!$report->reviewer) return;

        $tokens = UserDevice::where('user_id',$report->reviewer->id)->pluck('device_token')->toArray();
        if(count($tokens)==0) return;

        foreach($tokens as $token){
            $report->reviewer->notify(new ReportNotification(
                "گزارش جدید برای بررسی",
                "یک گزارش جدید توسط {$report->user->name} ثبت شده است."
            ));
        }
    }

    // نوتیفیکیشن برای نویسنده گزارش
    private function notifyUser($report,$message)
    {
        $tokens = UserDevice::where('user_id',$report->user_id)->pluck('device_token')->toArray();
        if(count($tokens)==0) return;

        foreach($tokens as $token){
            $report->user->notify(new ReportNotification(
                "وضعیت گزارش شما تغییر کرد",
                $message
            ));
        }
    }
}
