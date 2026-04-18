@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">新增客戶</h1>
    <a href="{{ route('backend.customer.index') }}" class="btn btn-secondary">返回列表</a>
</div>

<div id="alertBox" class="d-none mb-3"></div>

<form id="customerForm">

    {{-- 基本資料 --}}
    <h5 class="mt-3 mb-3 border-bottom pb-1">基本資料</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">姓名 <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">性別</label>
            <select name="gender" class="form-select">
                <option value="">不指定</option>
                <option value="M">男</option>
                <option value="F">女</option>
                <option value="other">其他</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">出生日期</label>
            <input type="date" name="birth_date" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">血型</label>
            <select name="blood_type" class="form-select">
                <option value="unknown">不明</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="AB">AB</option>
                <option value="O">O</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">手機 <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">身分證字號</label>
            <input type="text" name="id_number" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">地址</label>
            <input type="text" name="address" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">職業</label>
            <input type="text" name="occupation" class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">來源</label>
            <select name="source" class="form-select">
                <option value="walk_in">現場</option>
                <option value="referral">介紹</option>
                <option value="online">線上</option>
                <option value="other">其他</option>
            </select>
        </div>
    </div>

    {{-- 緊急聯絡人 --}}
    <h5 class="mt-4 mb-3 border-bottom pb-1">緊急聯絡人</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">姓名</label>
            <input type="text" name="emergency_contact" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">電話</label>
            <input type="text" name="emergency_phone" class="form-control">
        </div>
    </div>

    {{-- 醫療資訊 --}}
    <h5 class="mt-4 mb-3 border-bottom pb-1">醫療資訊</h5>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">過敏史</label>
            <textarea name="allergies" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-md-6">
            <label class="form-label">病史</label>
            <textarea name="medical_history" class="form-control" rows="3"></textarea>
        </div>
    </div>

    {{-- 備註 --}}
    <h5 class="mt-4 mb-3 border-bottom pb-1">其他</h5>
    <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label">備註</label>
            <textarea name="notes" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-md-3">
            <div class="form-check mt-2">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" checked>
                <label class="form-check-label" for="is_active">啟用此客戶</label>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" id="submitBtn" class="btn btn-success">
            <i class="fa-solid fa-floppy-disk"></i> 儲存
        </button>
        <a href="{{ route('backend.customer.index') }}" class="btn btn-secondary ms-2">取消</a>
    </div>
</form>

<script>
document.getElementById('customerForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const btn = document.getElementById('submitBtn');
    const alertBox = document.getElementById('alertBox');

    // 收集欄位
    const data = {};
    form.querySelectorAll('[name]').forEach(el => {
        if (el.type === 'checkbox') {
            data[el.name] = el.checked ? 1 : 0;
        } else if (el.value !== '') {
            data[el.name] = el.value;
        }
    });

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> 儲存中...';

    try {
        const res = await fetch('{{ route('backend.customer.store') }}', {
            method: 'POST',
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
            alertBox.textContent = json.message ?? '新增成功！';
            alertBox.classList.remove('d-none');
            setTimeout(() => {
                window.location.href = '{{ route('backend.customer.index') }}';
            }, 800);
        } else {
            // validation errors
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
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> 儲存';
    }
});
</script>
@endsection
