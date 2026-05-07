@extends('layouts.member_panel')

@section('title', '帳號安全 — DielBeauty')

@section('content')

{{-- 麵包屑 --}}
<nav class="mb-4" style="font-size:0.8rem;">
    <span class="text-muted">
        <i class="fa-solid fa-shield-halved me-1"></i>帳號安全
        <span class="mx-1 text-muted opacity-50">/</span>
        修改密碼
    </span>
</nav>

<div class="row justify-content-center">
<div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">

{{-- ── Step 1：驗證身份 ── --}}
<div id="step1-wrap">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            {{-- Icon --}}
            <div class="text-center mb-4">
                <div style="width:68px;height:68px;
                            background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);
                            border-radius:20px;display:inline-flex;align-items:center;
                            justify-content:center;font-size:1.7rem;color:#fff;
                            box-shadow:0 8px 24px rgba(102,126,234,.35);">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <h5 class="fw-bold mt-3 mb-1">驗證您的身份</h5>
                <p class="text-muted mb-0" style="font-size:0.85rem;">
                    為保護您的帳號安全，請先輸入目前密碼
                </p>
            </div>

            {{-- 錯誤（目前密碼不正確） --}}
            @if ($errors->has('current_password'))
                <div class="alert alert-danger d-flex align-items-center gap-2 py-2 mb-3"
                     style="font-size:0.85rem;">
                    <i class="fa-solid fa-circle-exclamation flex-shrink-0"></i>
                    {{ $errors->first('current_password') }}
                </div>
            @endif

            {{-- new_password 驗證失敗時的提示 --}}
            @if ($errors->has('new_password') && !$errors->has('current_password'))
                <div class="alert alert-warning d-flex align-items-center gap-2 py-2 mb-3"
                     style="font-size:0.85rem;">
                    <i class="fa-solid fa-triangle-exclamation flex-shrink-0"></i>
                    新密碼不符合要求，請重新輸入目前密碼後繼續修改。
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label small fw-semibold mb-1">目前密碼</label>
                <div class="input-group">
                    <input type="password" id="step1-pwd"
                           class="form-control"
                           placeholder="請輸入目前使用的密碼"
                           autocomplete="current-password">
                    <button type="button" class="btn btn-outline-secondary toggle-eye"
                            data-target="step1-pwd" tabindex="-1">
                        <i class="fa fa-eye"></i>
                    </button>
                </div>
                <div id="step1-empty-hint" class="text-danger small mt-1" style="display:none;">
                    請輸入目前密碼
                </div>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="button" class="btn btn-primary btn-lg" id="step1-next">
                    <i class="fa-solid fa-arrow-right me-2"></i>繼續
                </button>
                <a href="{{ route('member.profile') }}" class="btn btn-outline-secondary">取消</a>
            </div>

        </div>
    </div>
</div>

{{-- ── Step 2：設定新密碼 ── --}}
<div id="step2-wrap" style="display:none;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">

            {{-- Icon --}}
            <div class="text-center mb-4">
                <div style="width:68px;height:68px;
                            background:linear-gradient(135deg,#11998e 0%,#38ef7d 100%);
                            border-radius:20px;display:inline-flex;align-items:center;
                            justify-content:center;font-size:1.7rem;color:#fff;
                            box-shadow:0 8px 24px rgba(17,153,142,.3);">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h5 class="fw-bold mt-3 mb-1">設定新密碼</h5>
                <p class="text-muted mb-0" style="font-size:0.85rem;">
                    請選擇一個安全性足夠的新密碼
                </p>
            </div>

            <form id="pwd-form" action="{{ route('member.password.update') }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="current_password" id="current-hidden">

                {{-- 新密碼 --}}
                <div class="mb-2">
                    <label class="form-label small fw-semibold mb-1">
                        新密碼 <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" name="new_password" id="new-pwd"
                               class="form-control @error('new_password') is-invalid @enderror"
                               placeholder="輸入新密碼" autocomplete="new-password" required>
                        <button type="button" class="btn btn-outline-secondary toggle-eye"
                                data-target="new-pwd" tabindex="-1">
                            <i class="fa fa-eye"></i>
                        </button>
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- 強度條 --}}
                <div class="mb-2">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="text-muted" style="font-size:0.75rem;">密碼強度</span>
                        <span id="strength-label" class="text-muted" style="font-size:0.75rem;">—</span>
                    </div>
                    <div class="progress" style="height:5px;border-radius:3px;">
                        <div id="strength-bar" class="progress-bar"
                             style="width:0%;transition:width .25s,background-color .25s;border-radius:3px;"></div>
                    </div>
                </div>

                {{-- 規則 Badges --}}
                <div class="d-flex flex-wrap gap-1 mb-3" id="pwd-rules">
                    @foreach ([
                        'length'  => '≥ 8 字元',
                        'lower'   => 'a-z',
                        'upper'   => 'A-Z',
                        'number'  => '0-9',
                        'special' => '特殊符號',
                    ] as $rule => $label)
                        <span class="badge rounded-pill pw-rule" data-rule="{{ $rule }}"
                              style="background:#f0f1f3;color:#9ca3af;font-size:0.7rem;
                                     font-weight:500;padding:4px 8px;transition:background .2s,color .2s;">
                            <i class="fa fa-times me-1" style="font-size:0.6rem;"></i>{{ $label }}
                        </span>
                    @endforeach
                </div>

                {{-- 確認密碼 --}}
                <div class="mb-4">
                    <label class="form-label small fw-semibold mb-1">
                        確認新密碼 <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="password" name="new_password_confirmation" id="confirm-pwd"
                               class="form-control"
                               placeholder="再次輸入新密碼"
                               autocomplete="new-password" required>
                        <button type="button" class="btn btn-outline-secondary toggle-eye"
                                data-target="confirm-pwd" tabindex="-1">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                    <div id="confirm-hint" class="mt-1 ps-1" style="display:none;">
                        <small id="confirm-msg" style="font-size:0.78rem;"></small>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg" id="save-btn">
                        <i class="fa-solid fa-check me-2"></i>儲存新密碼
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="step2-back">返回</button>
                </div>

            </form>
        </div>
    </div>
</div>

</div>
</div>

@endsection

@push('scripts')
<script>
(function () {
    const step1Wrap  = document.getElementById('step1-wrap');
    const step2Wrap  = document.getElementById('step2-wrap');
    const step1Pwd   = document.getElementById('step1-pwd');
    const step1Next  = document.getElementById('step1-next');
    const step2Back  = document.getElementById('step2-back');
    const emptyHint  = document.getElementById('step1-empty-hint');
    const curHidden  = document.getElementById('current-hidden');
    const newPwd     = document.getElementById('new-pwd');
    const confirmPwd = document.getElementById('confirm-pwd');

    function showStep(n) {
        step1Wrap.style.display = n === 1 ? '' : 'none';
        step2Wrap.style.display = n === 2 ? '' : 'none';
        if (n === 1 && step1Pwd) step1Pwd.focus();
        if (n === 2 && newPwd)   newPwd.focus();
    }

    step1Next.addEventListener('click', function () {
        if (!step1Pwd.value.trim()) {
            step1Pwd.classList.add('is-invalid');
            emptyHint.style.display = '';
            step1Pwd.focus();
            return;
        }
        step1Pwd.classList.remove('is-invalid');
        emptyHint.style.display = 'none';
        curHidden.value = step1Pwd.value;
        showStep(2);
    });

    // Enter on step1 triggers next
    step1Pwd.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); step1Next.click(); }
    });

    step2Back.addEventListener('click', function () {
        curHidden.value = '';
        showStep(1);
    });

    // ── 密碼規則即時驗證 ──
    const rules = {
        length:  v => v.length >= 8,
        lower:   v => /[a-z]/.test(v),
        upper:   v => /[A-Z]/.test(v),
        number:  v => /[0-9]/.test(v),
        special: v => /[@$!%*#?&^_\-]/.test(v),
    };
    const strengthLabels = ['', '弱', '弱', '普通', '良好', '強'];
    const strengthColors = ['', '#dc3545', '#fd7e14', '#ffc107', '#20c997', '#198754'];

    if (newPwd) {
        newPwd.addEventListener('input', function () {
            const v = this.value;
            let score = 0;
            document.querySelectorAll('#pwd-rules .pw-rule').forEach(function (badge) {
                const pass = rules[badge.dataset.rule](v);
                if (pass) score++;
                badge.style.background = pass ? '#198754' : '#f0f1f3';
                badge.style.color      = pass ? '#fff'    : '#9ca3af';
                const ico = badge.querySelector('i');
                ico.className = (pass ? 'fa fa-check' : 'fa fa-times') + ' me-1';
                ico.style.fontSize = '0.6rem';
            });
            const bar   = document.getElementById('strength-bar');
            const label = document.getElementById('strength-label');
            bar.style.width           = (score / 5 * 100) + '%';
            bar.style.backgroundColor = strengthColors[score] || '#dee2e6';
            label.textContent         = strengthLabels[score] || '—';
            label.style.color         = strengthColors[score] || '#9ca3af';
            checkConfirm();
        });
    }

    if (confirmPwd) confirmPwd.addEventListener('input', checkConfirm);

    function checkConfirm() {
        const hint = document.getElementById('confirm-hint');
        const msg  = document.getElementById('confirm-msg');
        if (!confirmPwd.value) { hint.style.display = 'none'; return; }
        hint.style.display = '';
        if (newPwd.value === confirmPwd.value) {
            msg.textContent = '✓ 密碼一致';
            msg.style.color = '#198754';
        } else {
            msg.textContent = '✗ 密碼不一致';
            msg.style.color = '#dc3545';
        }
    }

    // ── 眼睛 Toggle ──
    document.querySelectorAll('.toggle-eye').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(this.dataset.target);
            const icon  = this.querySelector('i');
            const show  = input.type === 'password';
            input.type  = show ? 'text' : 'password';
            icon.className = show ? 'fa fa-eye-slash' : 'fa fa-eye';
        });
    });

    // 有 new_password 錯誤時停在 step 1（讓使用者重走流程）
    @if ($errors->has('current_password') || $errors->has('new_password'))
        showStep(1);
    @endif
})();
</script>
@endpush
