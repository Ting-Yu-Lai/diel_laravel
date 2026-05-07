@extends('layouts.member_panel')

@section('title', '個人資料 — DielBeauty')

@section('content')

@php
    $customer   = $member->customer;
    $genderMap  = ['M' => '男', 'F' => '女', 'other' => '其他'];
    $gender     = $customer?->gender ? ($genderMap[$customer->gender] ?? '—') : '—';
    $birthDate  = $customer?->birth_date?->format('Y年m月d日') ?? '—';
    $address    = $member->address ?: ($customer?->address ?? null);
    $initial    = mb_substr($member->full_name, 0, 1);
@endphp

{{-- ── Identity Header ── --}}
<div class="card shadow-sm mb-4 overflow-hidden border-0">
    <div class="card-body p-0">
        <div style="background:linear-gradient(135deg,#1a1a2e 0%,#2c2c4a 100%); padding:2rem 2rem 1.6rem;">
            <div class="d-flex align-items-center gap-4">
                <div style="width:72px;height:72px;border-radius:50%;
                            background:rgba(212,163,115,0.18);border:2px solid rgba(212,163,115,0.45);
                            display:flex;align-items:center;justify-content:center;
                            font-size:1.75rem;color:#d4a373;flex-shrink:0;
                            font-family:'Cormorant Garamond',Georgia,serif;font-weight:600;
                            letter-spacing:0;">
                    {{ $initial }}
                </div>
                <div class="min-w-0">
                    <div style="color:#fff;font-size:1.3rem;font-weight:600;line-height:1.2;">
                        {{ $member->full_name }}
                    </div>
                    <div style="color:rgba(255,255,255,0.5);font-size:0.82rem;margin-top:4px;">
                        {{ $member->email }}
                    </div>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @if ($member->line_user_id)
                            <span class="badge" style="background:rgba(0,185,0,0.75);font-size:0.7rem;font-weight:500;">
                                <i class="fa-brands fa-line me-1"></i>LINE 已綁定
                            </span>
                        @endif
                        <span class="badge" style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.55);font-size:0.7rem;font-weight:400;">
                            上次登入 {{ $member->last_login_at?->format('Y-m-d H:i') ?? '首次登入' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── 基本資料（唯讀） ── --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom fw-semibold d-flex align-items-center gap-2">
        <i class="fa-solid fa-circle-info text-secondary" style="font-size:.85rem;"></i>
        基本資料
        <span class="badge bg-light text-secondary fw-normal ms-1" style="font-size:0.68rem;">唯讀</span>
    </div>
    <div class="card-body p-0">

        {{-- 姓名 --}}
        <div class="d-flex align-items-start px-4 py-3 border-bottom">
            <div class="text-muted" style="width:90px;font-size:0.8rem;flex-shrink:0;padding-top:3px;">姓名</div>
            <div class="flex-grow-1">
                <span class="fw-medium">{{ $member->full_name }}</span>
            </div>
            <div class="text-muted small d-none d-sm-block" style="font-size:0.75rem;white-space:nowrap;">
                如需修改請聯絡診所
            </div>
        </div>

        {{-- 生日 --}}
        <div class="d-flex align-items-start px-4 py-3 border-bottom">
            <div class="text-muted" style="width:90px;font-size:0.8rem;flex-shrink:0;padding-top:3px;">生日</div>
            <div class="flex-grow-1">
                <span class="{{ $birthDate === '—' ? 'text-muted' : 'fw-medium' }}">{{ $birthDate }}</span>
            </div>
        </div>

        {{-- 性別 --}}
        <div class="d-flex align-items-start px-4 py-3">
            <div class="text-muted" style="width:90px;font-size:0.8rem;flex-shrink:0;padding-top:3px;">性別</div>
            <div class="flex-grow-1">
                <span class="{{ $gender === '—' ? 'text-muted' : 'fw-medium' }}">{{ $gender }}</span>
            </div>
        </div>

    </div>
</div>

{{-- ── 聯絡資訊（可編輯） ── --}}
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom fw-semibold d-flex align-items-center gap-2">
        <i class="fa-solid fa-address-card text-primary" style="font-size:.85rem;"></i>
        聯絡資訊
    </div>
    <div class="card-body p-0" id="contact-card">

        {{-- 電子郵件 --}}
        <div class="border-bottom" id="row-email">
            <div id="view-email" class="d-flex align-items-center px-4 py-3">
                <div class="text-muted" style="width:90px;font-size:0.8rem;flex-shrink:0;">電子郵件</div>
                <div class="flex-grow-1 fw-medium">{{ $member->email }}</div>
                <button type="button" class="btn btn-link btn-sm text-primary p-0 ms-3"
                        style="font-size:0.8rem;white-space:nowrap;"
                        onclick="openEdit('email')">
                    <i class="fa-solid fa-pen-to-square me-1"></i>編輯
                </button>
            </div>
            <div id="edit-email" class="px-4 pb-3 pt-1 bg-light" style="display:none;">
                <form action="{{ route('member.profile.update') }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="phone"   value="{{ $member->phone }}">
                    <input type="hidden" name="address" value="{{ $address }}">
                    <div class="mb-2 mt-2" style="max-width:340px;">
                        <label class="form-label small mb-1 text-muted">電子郵件</label>
                        <input type="email" name="email"
                               class="form-control form-control-sm @error('email') is-invalid @enderror"
                               value="{{ old('email', $member->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary btn-sm px-3">儲存</button>
                        <button type="button" class="btn btn-link btn-sm text-muted p-0 text-decoration-none"
                                onclick="closeEdit('email')">取消</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 手機號碼 --}}
        <div class="border-bottom" id="row-phone">
            <div id="view-phone" class="d-flex align-items-center px-4 py-3">
                <div class="text-muted" style="width:90px;font-size:0.8rem;flex-shrink:0;">手機號碼</div>
                <div class="flex-grow-1 fw-medium">{{ $member->formatted_phone ?: '—' }}</div>
                <button type="button" class="btn btn-link btn-sm text-primary p-0 ms-3"
                        style="font-size:0.8rem;white-space:nowrap;"
                        onclick="openEdit('phone')">
                    <i class="fa-solid fa-pen-to-square me-1"></i>編輯
                </button>
            </div>
            <div id="edit-phone" class="px-4 pb-3 pt-1 bg-light" style="display:none;">
                <form action="{{ route('member.profile.update') }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="email"   value="{{ $member->email }}">
                    <input type="hidden" name="address" value="{{ $address }}">
                    <div class="mb-2 mt-2" style="max-width:340px;">
                        <label class="form-label small mb-1 text-muted">手機號碼</label>
                        <input type="tel" name="phone"
                               class="form-control form-control-sm @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $member->formatted_phone) }}"
                               placeholder="0912-345-678" required>
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary btn-sm px-3">儲存</button>
                        <button type="button" class="btn btn-link btn-sm text-muted p-0 text-decoration-none"
                                onclick="closeEdit('phone')">取消</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 住家地址 --}}
        <div id="row-address">
            <div id="view-address" class="d-flex align-items-center px-4 py-3">
                <div class="text-muted" style="width:90px;font-size:0.8rem;flex-shrink:0;">住家地址</div>
                <div class="flex-grow-1 {{ $address ? 'fw-medium' : 'text-muted' }}">
                    {{ $address ?: '尚未填寫' }}
                </div>
                <button type="button" class="btn btn-link btn-sm text-primary p-0 ms-3"
                        style="font-size:0.8rem;white-space:nowrap;"
                        onclick="openEdit('address')">
                    <i class="fa-solid fa-pen-to-square me-1"></i>編輯
                </button>
            </div>
            <div id="edit-address" class="px-4 pb-3 pt-1 bg-light" style="display:none;">
                <form action="{{ route('member.profile.update') }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="email" value="{{ $member->email }}">
                    <input type="hidden" name="phone" value="{{ $member->phone }}">
                    <div class="mb-2 mt-2" style="max-width:460px;">
                        <label class="form-label small mb-1 text-muted">住家地址</label>
                        <input type="text" name="address"
                               class="form-control form-control-sm @error('address') is-invalid @enderror"
                               value="{{ old('address', $address) }}"
                               placeholder="請輸入完整地址">
                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <button type="submit" class="btn btn-primary btn-sm px-3">儲存</button>
                        <button type="button" class="btn btn-link btn-sm text-muted p-0 text-decoration-none"
                                onclick="closeEdit('address')">取消</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

{{-- ── LINE 通知綁定 ── --}}
<div class="card shadow-sm">
    <div class="card-header bg-white border-bottom fw-semibold d-flex align-items-center gap-2">
        <i class="fa-brands fa-line text-success" style="font-size:.9rem;"></i>
        LINE 通知
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="flex-grow-1">
                @if ($member->line_user_id)
                    <div class="fw-medium small">已綁定 LINE 帳號</div>
                    <div class="text-muted" style="font-size:0.78rem;margin-top:2px;">
                        術後追蹤提醒將透過 LINE 通知您
                    </div>
                @else
                    <div class="fw-medium small">尚未綁定 LINE 帳號</div>
                    <div class="text-muted" style="font-size:0.78rem;margin-top:2px;">
                        綁定後可接收術後追蹤提醒
                    </div>
                @endif
            </div>
            @if ($member->line_user_id)
                <form action="{{ route('member.line.unbind') }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm px-3"
                            onclick="return confirm('確定要解除 LINE 綁定嗎？')">
                        解除綁定
                    </button>
                </form>
            @else
                <a href="{{ route('member.line.bind') }}" class="btn btn-success btn-sm px-4">
                    <i class="fa-brands fa-line me-1"></i>立即綁定
                </a>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const FIELDS = ['email', 'phone', 'address'];

    function openEdit(field) {
        FIELDS.forEach(function (f) {
            document.getElementById('view-' + f).style.display = f === field ? 'none' : '';
            document.getElementById('edit-' + f).style.display = f === field ? ''    : 'none';
        });
        const input = document.querySelector('#edit-' + field + ' input[name=' + field + ']');
        if (input) { input.focus(); input.select(); }
    }

    function closeEdit(field) {
        document.getElementById('view-' + field).style.display = '';
        document.getElementById('edit-' + field).style.display = 'none';
    }

    // expose to inline onclick
    window.openEdit  = openEdit;
    window.closeEdit = closeEdit;

    // 有驗證錯誤時自動展開對應欄位
    @if ($errors->has('email'))
        openEdit('email');
    @elseif ($errors->has('phone'))
        openEdit('phone');
    @elseif ($errors->has('address'))
        openEdit('address');
    @endif
})();
</script>
@endpush
