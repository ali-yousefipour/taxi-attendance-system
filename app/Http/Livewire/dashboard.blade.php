<div>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5>کل گزارش‌ها</h5>
                    <h3>{{ $totalReports }}</h3>
                </div>
            </div>
        </div>

        @foreach(['submitted','in_review','approved','rejected'] as $status)
        <div class="col-md-3">
            <div class="card text-white @if($status=='approved') bg-success @elseif($status=='rejected') bg-danger @elseif($status=='in_review') bg-warning @else bg-info @endif">
                <div class="card-body">
                    <h5>{{ $status }}</h5>
                    <h3>{{ $statusCounts[$status] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <h4>گزارش‌های اخیر</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>عنوان</th>
                <th>نویسنده</th>
                <th>مسئول بررسی فعلی</th>
                <th>وضعیت</th>
                <th>تاریخ</th>
                <th>جزئیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($recentReports as $report)
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

    <h4>نمودار وضعیت گزارش‌ها</h4>
    <canvas id="statusChart"></canvas>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('livewire:load', function () {
        var ctx = document.getElementById('statusChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['submitted','in_review','approved','rejected'],
                datasets: [{
                    data: [
                        @json($statusCounts['submitted'] ?? 0),
                        @json($statusCounts['in_review'] ?? 0),
                        @json($statusCounts['approved'] ?? 0),
                        @json($statusCounts['rejected'] ?? 0)
                    ],
                    backgroundColor: ['#17a2b8','#ffc107','#28a745','#dc3545']
                }]
            },
            options: {}
        });

        Livewire.on('reportUpdated', data => {
            chart.data.datasets[0].data = [
                data.submitted ?? 0,
                data.in_review ?? 0,
                data.approved ?? 0,
                data.rejected ?? 0
            ];
            chart.update();
        });
    });
    </script>
</div>
