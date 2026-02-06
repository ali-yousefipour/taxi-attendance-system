@extends('layouts.app')

@section('content')
<div class="container">
    <h1>جزئیات گزارش</h1>

    <div class="card mb-3">
        <div class="card-header">
            <strong>{{ $report->title }}</strong>
        </div>
        <div class="card-body">
            <p>{{ $report->description }}</p>
            <p><strong>نویسنده:</strong> {{ $report->user->name }}</p>
            <p><strong>مسئول فعلی بررسی:</strong> {{ $report->reviewer?->name ?? '-' }}</p>
            <p><strong>وضعیت:</strong> {{ $report->status }}</p>
        </div>
    </div>

    <h4>یادداشت‌ها</h4>
    @foreach($report->notes as $note)
    <div class="alert alert-secondary">
        <strong>{{ $note->user->name }}:</strong> {{ $note->note }}
        <span class="float-end">{{ $note->created_at->format('Y-m-d H:i') }}</span>
    </div>
    @endforeach

    <form action="{{ route('reports.note',$report->id) }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>اضافه کردن یادداشت</label>
            <textarea name="note" class="form-control" rows="3" required></textarea>
        </div>
        <button class="btn btn-primary">ثبت یادداشت</button>
    </form>

    @if($report->status != 'approved' && $report->status != 'rejected')
    <div class="mt-3">
        <form action="{{ route('reports.approve',$report->id) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success">تایید</button>
        </form>
        <form action="{{ route('reports.reject',$report->id) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-danger">رد</button>
        </form>
    </div>
    @endif

    <h4 class="mt-4">تاریخچه وضعیت</h4>
    <ul>
        @foreach($report->statusHistory as $history)
        <li>
            <strong>{{ $history->changed_by == auth()->id() ? 'شما' : $history->changed_by }}:</strong>
            از "{{ $history->old_status }}" به "{{ $history->new_status }}" 
            در {{ $history->created_at->format('Y-m-d H:i') }}
        </li>
        @endforeach
    </ul>
</div>
@endsection
