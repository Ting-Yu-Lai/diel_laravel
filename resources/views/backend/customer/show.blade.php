@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">
        客戶檔案 — {{ $customer->name }}
        @if($customer->is_active)
            <span class="badge bg-success fs-6 ms-2">啟用</span>
        @else
            <span class="badge bg-secondary fs-6 ms-2">停用</span>
        @endif
    </h1>
    <div>
        <a href="{{ route('backend.customer.edit', $customer->id) }}" class="btn btn-primary">
            <i class="fa-solid fa-pen"></i> 編輯
        </a>
        <a href="{{ route('backend.customer.index') }}" class="btn btn-secondary ms-2">返回列表</a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row g-4">

    {{-- 基本資料 --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-circle-user me-1"></i> 基本資料
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">姓名</dt>
                    <dd class="col-sm-8">{{ $customer->name }}</dd>

                    <dt class="col-sm-4">性別</dt>
                    <dd class="col-sm-8">
                        @switch($customer->gender)
                            @case('M') 男 @break
                            @case('F') 女 @break
                            @case('other') 其他 @break
                            @default —
                        @endswitch
                    </dd>

                    <dt class="col-sm-4">出生日期</dt>
                    <dd class="col-sm-8">{{ $customer->birth_date?->format('Y-m-d') ?? '—' }}</dd>

                    <dt class="col-sm-4">血型</dt>
                    <dd class="col-sm-8">{{ $customer->blood_type === 'unknown' ? '不明' : ($customer->blood_type ?? '—') }}</dd>

                    <dt class="col-sm-4">身分證字號</dt>
                    <dd class="col-sm-8">{{ $customer->id_number ?? '—' }}</dd>

                    <dt class="col-sm-4">職業</dt>
                    <dd class="col-sm-8">{{ $customer->occupation ?? '—' }}</dd>

                    <dt class="col-sm-4">來源</dt>
                    <dd class="col-sm-8">
                        @switch($customer->source)
                            @case('walk_in')  現場 @break
                            @case('referral') 介紹 @break
                            @case('online')   線上 @break
                            @default          其他
                        @endswitch
                    </dd>

                    <dt class="col-sm-4">建立日期</dt>
                    <dd class="col-sm-8">{{ $customer->created_at->format('Y-m-d') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- 聯絡資訊 --}}
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-address-book me-1"></i> 聯絡資訊
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">手機</dt>
                    <dd class="col-sm-8">{{ $customer->formatted_phone }}</dd>

                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $customer->email ?? '—' }}</dd>

                    <dt class="col-sm-4">地址</dt>
                    <dd class="col-sm-8">{{ $customer->address ?? '—' }}</dd>

                    <dt class="col-sm-4 mt-3">緊急聯絡人</dt>
                    <dd class="col-sm-8 mt-3">{{ $customer->emergency_contact ?? '—' }}</dd>

                    <dt class="col-sm-4">緊急電話</dt>
                    <dd class="col-sm-8">{{ $customer->formatted_emergency_phone ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- 醫療資訊 --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-notes-medical me-1"></i> 醫療資訊
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">過敏史</dt>
                    <dd class="col-sm-8" style="white-space: pre-wrap;">{{ $customer->allergies ?? '—' }}</dd>

                    <dt class="col-sm-4 mt-2">病史</dt>
                    <dd class="col-sm-8 mt-2" style="white-space: pre-wrap;">{{ $customer->medical_history ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- 備註 --}}
    <div class="col-md-6">
        <div class="card">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-clipboard me-1"></i> 備註
            </div>
            <div class="card-body">
                <p class="mb-0" style="white-space: pre-wrap;">{{ $customer->notes ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- 標籤 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-bold">
                <i class="fa-solid fa-tags me-1"></i> 標籤
            </div>
            <div class="card-body">
                @if ($customer->tags->isEmpty())
                    <span class="text-muted">尚未指派標籤</span>
                @else
                    @php
                        $tagsByCategory = $customer->tags->groupBy(fn($tag) => $tag->category->name ?? '未分類');
                    @endphp
                    @foreach ($tagsByCategory as $categoryName => $tags)
                        <div class="mb-2">
                            <small class="text-muted me-2">{{ $categoryName }}</small>
                            @foreach ($tags as $tag)
                                <span class="badge bg-primary me-1">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    {{-- 會員帳號 --}}
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                <span><i class="fa-solid fa-user-shield me-1"></i> 會員帳號</span>
            </div>
            <div class="card-body">
                @if ($customer->member)
                    @php $member = $customer->member; @endphp
                    <dl class="row mb-3">
                        <dt class="col-sm-3">Email</dt>
                        <dd class="col-sm-9">{{ $member->email }}</dd>
                        <dt class="col-sm-3">手機</dt>
                        <dd class="col-sm-9">{{ $member->formatted_phone ?? $member->phone ?? '—' }}</dd>
                        <dt class="col-sm-3">最後登入</dt>
                        <dd class="col-sm-9">{{ $member->last_login_at?->format('Y-m-d H:i') ?? '從未登入' }}</dd>
                        <dt class="col-sm-3">LINE 綁定</dt>
                        <dd class="col-sm-9">
                            @if($member->line_user_id)
                                <span class="badge bg-success">已綁定</span>
                            @else
                                <span class="badge bg-secondary">未綁定</span>
                            @endif
                        </dd>
                    </dl>
                    <button type="button" class="btn btn-warning btn-sm me-2"
                        onclick="handleResetPassword({{ $customer->id }})">
                        <i class="fa-solid fa-key"></i> 重設密碼
                    </button>
                    <form method="POST" action="{{ route('backend.customer.member.unlink', $customer->id) }}"
                        class="d-inline"
                        onsubmit="return confirm('確定要解除此客戶與會員帳號的關聯？')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="fa-solid fa-link-slash"></i> 解除關聯
                        </button>
                    </form>
                @elseif ($customer->email && $customer->id_number && $customer->birth_date)
                    <p class="text-muted mb-3">此客戶尚未建立會員帳號，資料齊全可自動建立。</p>
                    <button type="button" class="btn btn-success btn-sm"
                        onclick="handleCreateMember({{ $customer->id }})">
                        <i class="fa-solid fa-user-plus"></i> 自動建立會員帳號
                    </button>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        尚未建立會員帳號，且缺少必要資料（Email、身分證字號、出生日期）。
                        <a href="{{ route('backend.customer.edit', $customer->id) }}" class="alert-link ms-1">前往編輯補齊</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- 密碼顯示 Modal --}}
<div class="modal fade" id="passwordModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalTitle">初始密碼</h5>
            </div>
            <div class="modal-body">
                <p id="passwordModalMessage" class="mb-3"></p>
                <div class="input-group">
                    <input type="text" id="passwordDisplay" class="form-control font-monospace fs-5"
                        readonly>
                    <button class="btn btn-outline-secondary" type="button" id="copyPasswordBtn">
                        <i class="fa-solid fa-copy"></i> 複製
                    </button>
                </div>
                <small class="text-muted mt-2 d-block">請告知客戶並提醒盡快修改密碼。</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="passwordModalConfirm">確認</button>
            </div>
        </div>
    </div>
</div>

<script>
function showPasswordModal(title, message, password, onConfirm) {
    document.getElementById('passwordModalTitle').textContent   = title;
    document.getElementById('passwordModalMessage').textContent = message;
    document.getElementById('passwordDisplay').value            = password;
    document.getElementById('passwordModalConfirm').onclick     = function () {
        bootstrap.Modal.getInstance(document.getElementById('passwordModal')).hide();
        if (onConfirm) onConfirm();
    };
    document.getElementById('copyPasswordBtn').onclick = function () {
        const val = document.getElementById('passwordDisplay').value;
        if (navigator.clipboard) {
            navigator.clipboard.writeText(val).then(() => {
                this.innerHTML = '<i class="fa-solid fa-check"></i> 已複製';
                setTimeout(() => { this.innerHTML = '<i class="fa-solid fa-copy"></i> 複製'; }, 2000);
            });
        } else {
            document.getElementById('passwordDisplay').select();
            document.execCommand('copy');
        }
    };
    new bootstrap.Modal(document.getElementById('passwordModal')).show();
}

function handleCreateMember(customerId) {
    fetch(`/backend/customer/${customerId}/member/create`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    })
    .then(r => r.json().then(data => ({ ok: r.ok, data })))
    .then(({ ok, data }) => {
        if (! ok) { alert(data.message); return; }
        if (data.initial_password) {
            showPasswordModal('會員帳號建立成功', data.message, data.initial_password, () => location.reload());
        } else {
            alert(data.message);
            location.reload();
        }
    })
    .catch(() => alert('網路錯誤，請稍後再試'));
}

function handleResetPassword(customerId) {
    if (! confirm('確定要重設此客戶的會員帳號密碼？')) return;
    fetch(`/backend/customer/${customerId}/member/reset-password`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
    })
    .then(r => r.json().then(data => ({ ok: r.ok, data })))
    .then(({ ok, data }) => {
        if (! ok) { alert(data.message); return; }
        showPasswordModal('密碼已重設', data.message, data.new_password, null);
    })
    .catch(() => alert('網路錯誤，請稍後再試'));
}
</script>
@endsection
