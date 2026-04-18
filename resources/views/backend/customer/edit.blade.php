@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">編輯客戶 — {{ $customer->name }}</h1>
    <a href="{{ route('backend.customer.show', $customer->id) }}" class="btn btn-secondary">返回檔案</a>
</div>

<div id="alertBox" class="d-none mb-3"></div>

<form id="customerForm">

    {{-- 基本資料 --}}
    <h5 class="mt-3 mb-3 border-bottom pb-1">基本資料</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">姓名 <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">性別</label>
            <select name="gender" class="form-select">
                <option value="">不指定</option>
                <option value="M"     {{ $customer->gender === 'M'     ? 'selected' : '' }}>男</option>
                <option value="F"     {{ $customer->gender === 'F'     ? 'selected' : '' }}>女</option>
                <option value="other" {{ $customer->gender === 'other' ? 'selected' : '' }}>其他</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">出生日期</label>
            <input type="date" name="birth_date" class="form-control"
                value="{{ $customer->birth_date?->format('Y-m-d') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">血型</label>
            <select name="blood_type" class="form-select">
                @foreach(['unknown' => '不明', 'A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O'] as $val => $label)
                    <option value="{{ $val }}" {{ $customer->blood_type === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">手機 <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $customer->email }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">身分證字號</label>
            <input type="text" name="id_number" class="form-control" value="{{ $customer->id_number }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">地址</label>
            <input type="text" name="address" class="form-control" value="{{ $customer->address }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">職業</label>
            <input type="text" name="occupation" class="form-control" value="{{ $customer->occupation }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">來源</label>
            <select name="source" class="form-select">
                @foreach(['walk_in' => '現場', 'referral' => '介紹', 'online' => '線上', 'other' => '其他'] as $val => $label)
                    <option value="{{ $val }}" {{ $customer->source === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- 緊急聯絡人 --}}
    <h5 class="mt-4 mb-3 border-bottom pb-1">緊急聯絡人</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">姓名</label>
            <input type="text" name="emergency_contact" class="form-control"
                value="{{ $customer->emergency_contact }}">
        </div>
        <div class="col-md-4">
            <label class="form-label">電話</label>
            <input type="text" name="emergency_phone" class="form-control"
                value="{{ $customer->emergency_phone }}">
        </div>
    </div>

    {{-- 醫療資訊 --}}
    <h5 class="mt-4 mb-3 border-bottom pb-1">醫療資訊</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">過敏史</label>
            <textarea name="allergies" class="form-control" rows="3">{{ $customer->allergies }}</textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">病史</label>
            <textarea name="medical_history" class="form-control" rows="3">{{ $customer->medical_history }}</textarea>
        </div>
    </div>

    {{-- 其他 --}}
    <h5 class="mt-4 mb-3 border-bottom pb-1">其他</h5>
    <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label">備註</label>
            <textarea name="notes" class="form-control" rows="3">{{ $customer->notes }}</textarea>
        </div>
        <div class="col-md-3">
            <div class="form-check mt-2">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                    {{ $customer->is_active ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">啟用此客戶</label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" id="submitBtn" class="btn btn-success">
            <i class="fa-solid fa-floppy-disk"></i> 儲存變更
        </button>
        <a href="{{ route('backend.customer.show', $customer->id) }}" class="btn btn-secondary ms-2">取消</a>
    </div>
</form>

<script>
document.getElementById('customerForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const btn = document.getElementById('submitBtn');
    const alertBox = document.getElementById('alertBox');

    const data = {};
    form.querySelectorAll('[name]').forEach(el => {
        if (el.type === 'checkbox') {
            data[el.name] = el.checked ? 1 : 0;
        } else {
            data[el.name] = el.value;
        }
    });

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> 儲存中...';

    try {
        const res = await fetch('{{ route('backend.customer.update', $customer->id) }}', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify(data),
        });

        const json = await res.json();

        if (res.ok) {
            alertBox.className = 'alert alert-success';
            alertBox.textContent = json.message ?? '更新成功！';
            alertBox.classList.remove('d-none');
            setTimeout(() => {
                window.location.href = '{{ route('backend.customer.show', $customer->id) }}';
            }, 800);
        } else {
            const errors = json.errors
                ? Object.values(json.errors).flat().join('\n')
                : (json.message ?? '發生錯誤');
            alertBox.className = 'alert alert-danger';
            alertBox.textContent = errors;
            alertBox.classList.remove('d-none');
        }
    } catch (err) {
        alertBox.className = 'alert alert-danger';
        alertBox.textContent = '網路錯誤，請稍後再試';
        alertBox.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> 儲存變更';
    }
});
</script>
@endsection
