@extends('layouts.app')

@section('content')
<div class="container">
    <h1>ثبت گزارش جدید</h1>

    <form action="{{ route('reports.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>عنوان</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>توضیحات</label>
            <textarea name="description" class="form-control" rows="5" required></textarea>
        </div>

        <button class="btn btn-success">ثبت گزارش</button>
    </form>
</div>
@endsection
