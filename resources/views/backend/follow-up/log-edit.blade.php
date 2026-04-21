@extends('backend.layouts.app')

@section('content')
@php
    $item   = $followUp->treatmentRecordItem;
    $record = $item->treatmentRecord;
@endphp

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">編輯追蹤紀錄</h1>
    <a href="{{ route('backend.follow-up.show', $followUp->id) }}" class="btn btn-secondary">返回術後追蹤</a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header fw-semibold">
                {{ $item->treatment->name ?? '—' }} ・ {{ $record->customer->name ?? '—' }}
            </div>
            <div class="card-body">
                <form action="{{ route('backend.follow-up.log.update', [$followUp->id, $log->id]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-semibold">第幾天 <span class="text-danger">*</span></label>
                        <input type="number" name="day_number" min="1"
                            class="form-control @error('day_number') is-invalid @enderror"
                            value="{{ old('day_number', $log->day_number) }}"
                            placeholder="例：1">
                        @error('day_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">追蹤內容 <span class="text-danger">*</span></label>
                        <textarea name="content" rows="6"
                            class="form-control @error('content') is-invalid @enderror"
                            maxlength="5000"
                            placeholder="填寫此次追蹤標題">{{ old('content', $log->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">儲存</button>
                        <a href="{{ route('backend.follow-up.show', $followUp->id) }}" class="btn btn-outline-secondary">取消</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
