<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportNote;
use App\Models\ReportStatusHistory;
use App\Models\UserDevice;
use App\Notifications\ReportNotification;
use App\Events\ReportStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;

class ReportController extends Controller
{
    // لیست همه گزارش‌ها
    public function index(Request $request)
    {
        $query = Report::with('user','reviewer');

        if($request->status){
            $query->where('status',$request->status);
        }

        $reports = $query->latest()->paginate(20);

        return view('reports.index', compact('reports'));
    }

    // فرم ثبت گزارش جدید
    public function create()
    {
        return view('reports.create');
    }

    // ذخیره گزارش جدید
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

        event(new ReportStatusChanged($report,"گزارش جدید ثبت شد"));

        \Livewire\emit('reportUpdated', $this->getStatusCounts());

        return redirect()->route('reports.index')->with('success','گزارش با موفقیت ثبت شد');
    }

    // مشاهده جزئیات گزارش
    public function show(Report $report)
    {
        $report->load('user','reviewer','notes.user','statusHistory');
        return view('reports.show', compact('report'));
    }

    // اضافه کردن یادداشت
    public function addNote(Request $request, Report $report)
    {
        $request->validate(['note'=>'required|string']);

        ReportNote::create([
            'report_id'=>$report->id,
            'user_id'=>Auth::id(),
            'note'=>$request->note
        ]);

        return back()->with('success','یادداشت اضافه شد');
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

        event(new ReportStatusChanged($report,"وضعیت گزارش تغییر کرد"));
        \Livewire\emit('reportUpdated', $this->getStatusCounts());

        return back()->with('success','گزارش تایید شد');
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

        event(new ReportStatusChanged($report,"گزارش رد شد"));
        \Livewire\emit('reportUpdated', $this->getStatusCounts());

        return back()->with('success','گزارش رد شد');
    }

    // متد داشبورد
    public function dashboard()
    {
        return view('dashboard');
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

    // نوتیفیکیشن مسئول بررسی
    private function notifyReviewer($report)
    {
        if(!$report->reviewer) return;

        $tokens = UserDevice::where('user_id',$report->reviewer->id)->pluck('device_token')->toArray();
        foreach($tokens as $token){
            $report->reviewer->notify(new ReportNotification(
                "گزارش جدید برای بررسی",
                "یک گزارش جدید توسط {$report->user->name} ثبت شده است."
            ));
        }
    }

    // نوتیفیکیشن نویسنده گزارش
    private function notifyUser($report,$message)
    {
        $tokens = UserDevice::where('user_id',$report->user_id)->pluck('device_token')->toArray();
        foreach($tokens as $token){
            $report->user->notify(new ReportNotification(
                "وضعیت گزارش شما تغییر کرد",
                $message
            ));
        }
    }

    // تعداد گزارش‌ها برای Livewire
    private function getStatusCounts()
    {
        return [
            'submitted' => Report::where('status','submitted')->count(),
            'in_review' => Report::where('status','in_review')->count(),
            'approved' => Report::where('status','approved')->count(),
            'rejected' => Report::where('status','rejected')->count(),
        ];
    }
}
