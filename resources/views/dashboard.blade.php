@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">داشبورد مدیریتی پیشرفته</h1>

    {{-- کارت‌های وضعیت --}}
    <div class="row mb-4">
        @php
            $statuses = ['submitted'=>'اطلاعات ثبت‌شده','in_review'=>'در حال بررسی','approved'=>'تایید شده','rejected'=>'رد شده'];
        @endphp
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>کل گزارش‌ها</h5>
                    <h3>{{ \App\Models\Report::count() }}</h3>
                </div>
            </div>
        </div>
        @foreach($statuses as $key=>$label)
        <div class="col-md-3">
            <div class="card text-white
                @if($key=='approved') bg-success
                @elseif($key=='rejected') bg-danger
                @elseif($key=='in_review') bg-warning
                @else bg-info @endif">
                <div class="card-body">
                    <h5>{{ $label }}</h5>
                    <h3>{{ \App\Models\Report::where('status',$key)->count() }}</h3>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- جدول گزارش‌ها --}}
    <h4>گزارش‌های اخیر</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-light">
                <tr>
                    <th>عنوان</th>
                    <th>نویسنده</th>
                    <th>مسئول بررسی</th>
                    <th>وضعیت</th>
                    <th>تاریخ</th>
                    <th>جزئیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\Report::latest()->limit(10)->get() as $report)
                <tr>
                    <td>{{ $report->title }}</td>
                    <td>{{ $report->user->name }}</td>
                    <td>{{ $report->reviewer?->name ?? '-' }}</td>
                    <td>{{ $report->status }}</td>
                    <td>{{ $report->created_at->format('Y-m-d') }}</td>
                    <td><a href="{{ route('reports.show',$report->id) }}" class="btn btn-sm btn-info">جزئیات</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- نمودار وضعیت گزارش‌ها --}}
    <h4 class="mt-4">نمودار وضعیت گزارش‌ها</h4>
    <canvas id="statusChart"></canvas>
</div>

{{-- Chart.js + Livewire --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="{{ asset('js/app.js') }}"></script>

<script>
document.addEventListener('livewire:load', function () {
    var ctx = document.getElementById('statusChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['submitted','in_review','approved','rejected'],
            datasets: [{
                data: [
                    @json(\App\Models\Report::where('status','submitted')->count()),
                    @json(\App\Models\Report::where('status','in_review')->count()),
                    @json(\App\Models\Report::where('status','approved')->count()),
                    @json(\App\Models\Report::where('status','rejected')->count())
                ],
                backgroundColor: ['#17a2b8','#ffc107','#28a745','#dc3545']
            }]
        },
        options: {}
    });

    // بروزرسانی زنده نمودار با Livewire
    Livewire.on('reportUpdated', data => {
        chart.data.datasets[0].data = [
            data.submitted ?? 0,
            data.in_review ?? 0,
            data.approved ?? 0,
            data.rejected ?? 0
        ];
        chart.update();
    });

    // نوتیفیکیشن وب با Echo
    Echo.private('reports.{{ auth()->id() }}')
        .listen('ReportStatusChanged', (e) => {
            alert(e.message);
            Livewire.emit('reportUpdated', {
                submitted: @json(\App\Models\Report::where('status','submitted')->count()),
                in_review: @json(\App\Models\Report::where('status','in_review')->count()),
                approved: @json(\App\Models\Report::where('status','approved')->count()),
                rejected: @json(\App\Models\Report::where('status','rejected')->count())
            });
        });
});
</script>

@livewireScripts
@endsection
