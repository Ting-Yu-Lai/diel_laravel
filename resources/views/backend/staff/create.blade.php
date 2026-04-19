@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">新增工作人員</h1>
    <a href="{{ route('backend.staff.index') }}" class="btn btn-secondary">返回列表</a>
</div>

<div id="alertBox" class="d-none mb-3"></div>

<form id="staffForm">

    <h5 class="mt-3 mb-3 border-bottom pb-1">基本資料</h5>
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">姓名 <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required maxlength="50">
        </div>
        <div class="col-md-3">
            <label class="form-label">職稱 <span class="text-danger">*</span></label>
            <select name="job_title_id" class="form-select" required>
                <option value="">請選擇職稱</option>
                @foreach ($jobTitles as $jt)
                    <option value="{{ $jt->id }}">{{ $jt->name }}</option>
                @endforeach
            </select>
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
            <label class="form-label">到職日</label>
            <input type="date" name="hire_date" class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">手機 <span class="text-danger">*</span></label>
            <input type="text" name="phone" class="form-control" required maxlength="20">
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" maxlength="100">
        </div>
        <div class="col-md-4">
            <div class="form-check mt-4">
                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" checked>
                <label class="form-check-label" for="is_active">在職中</label>
            </div>
        </div>
    </div>

    <h5 class="mt-4 mb-3 border-bottom pb-1">備註</h5>
    <div class="row g-3">
        <div class="col-md-12">
            <textarea name="notes" class="form-control" rows="3" placeholder="備註（選填）"></textarea>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" id="submitBtn" class="btn btn-success">
            <i class="fa-solid fa-floppy-disk"></i> 儲存
        </button>
        <a href="{{ route('backend.staff.index') }}" class="btn btn-secondary ms-2">取消</a>
    </div>
</form>

<script>
document.getElementById('staffForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = e.target;
    const btn = document.getElementById('submitBtn');
    const alertBox = document.getElementById('alertBox');

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
        const res = await fetch('{{ route('backend.staff.store') }}', {
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
                window.location.href = '{{ route('backend.staff.index') }}';
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
        btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> 儲存';
    }
});
</script>
@endsection
