@extends('layouts.app')

@section('content')
<div class="container">
    <h1>لیست گزارش‌ها</h1>
    <a href="{{ route('reports.create') }}" class="btn btn-primary mb-3">ثبت گزارش جدید</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>عنوان</th>
                <th>نویسنده</th>
                <th>مسئول بررسی فعلی</th>
                <th>وضعیت</th>
                <th>تاریخ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->title }}</td>
                <td>{{ $report->user->name }}</td>
                <td>{{ $report->reviewer?->name ?? '-' }}</td>
                <td>{{ $report->status }}</td>
                <td>{{ $report->created_at->format('Y-m-d') }}</td>
                <td>
                    <a href="{{ route('reports.show',$report->id) }}" class="btn btn-sm btn-info">جزئیات</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
