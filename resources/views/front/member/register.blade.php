@extends('layouts.login')

@section('title', '會員註冊 — DielBeauty')

@section('content')
<div class="login-box" style="max-width:480px;">
    <h2 class="text-center mb-1" style="font-family:'Cormorant Garamond',serif; font-weight:400; letter-spacing:0.1em; font-size:1.6rem;">
        會員註冊
    </h2>
    <p class="text-center mb-4" style="font-size:0.72rem; letter-spacing:0.18em; color:rgba(239,230,221,0.4);">
        MEMBER REGISTRATION
    </p>

    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <ul class="mb-0 ps-3" style="font-size:0.85rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('member.register') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="full_name" class="form-label">姓名 <span class="text-gold">*</span></label>
            <input type="text" name="full_name" id="full_name"
                   class="form-control @error('full_name') is-invalid @enderror"
                   value="{{ old('full_name') }}"
                   placeholder="請輸入真實姓名" required>
            @error('full_name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">電子郵件 <span class="text-gold">*</span></label>
            <input type="email" name="email" id="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="example@mail.com" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">手機號碼 <span class="text-gold">*</span></label>
            <input type="tel" name="phone" id="phone"
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone') }}"
                   placeholder="0912345678" required>
            @error('phone')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">密碼 <span class="text-gold">*</span></label>
            <div class="input-group">
                <input type="password" name="password" id="password"
                       class="form-control @error('password') is-invalid @enderror"
                       placeholder="至少 8 位，含大小寫字母、數字、特殊符號" required>
                <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                    <i class="fa fa-eye" id="toggleIcon"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mt-2 ps-1" id="pwdRules">
                @foreach ([
                    'length'  => '長度至少 8 個字元',
                    'lower'   => '包含小寫字母（a-z）',
                    'upper'   => '包含大寫字母（A-Z）',
                    'number'  => '包含數字（0-9）',
                    'special' => '包含特殊符號（@$!%*#?&amp;^_-）',
                ] as $rule => $label)
                    <small class="d-block pw-rule" data-rule="{{ $rule }}" style="color:rgba(239,230,221,0.45);">
                        <i class="fa fa-times-circle me-1" style="color:#c07070;"></i>{{ $label }}
                    </small>
                @endforeach
            </div>
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="form-label">確認密碼 <span class="text-gold">*</span></label>
            <div class="input-group">
                <input type="password" name="password_confirmation" id="password_confirmation"
                       class="form-control" placeholder="再次輸入密碼" required>
                <button type="button" class="btn btn-outline-secondary" id="toggleConfirm" tabindex="-1">
                    <i class="fa fa-eye" id="toggleConfirmIcon"></i>
                </button>
            </div>
            <div class="mt-1 ps-1" id="confirmHint" style="display:none;">
                <small id="confirmMsg"></small>
            </div>
        </div>

        <div class="d-grid mb-3">
            <button type="submit" class="btn btn-login">註冊</button>
        </div>
        <div class="text-center" style="font-size:0.82rem;">
            <a href="{{ route('member.loginForm') }}"
               style="color:rgba(214,179,112,0.7); letter-spacing:0.04em;">
                已有帳號？前往登入
            </a>
        </div>
    </form>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script>
(function () {
    const pwdInput  = document.getElementById('password');
    const cfmInput  = document.getElementById('password_confirmation');
    const toggleBtn = document.getElementById('togglePassword');
    const toggleIcon= document.getElementById('toggleIcon');
    const cfmBtn    = document.getElementById('toggleConfirm');
    const cfmIcon   = document.getElementById('toggleConfirmIcon');
    const confirmHint = document.getElementById('confirmHint');
    const confirmMsg  = document.getElementById('confirmMsg');

    const rules = {
        length:  v => v.length >= 8,
        lower:   v => /[a-z]/.test(v),
        upper:   v => /[A-Z]/.test(v),
        number:  v => /[0-9]/.test(v),
        special: v => /[@$!%*#?&^_\-]/.test(v),
    };

    pwdInput.addEventListener('input', function () {
        const v = this.value;
        document.querySelectorAll('#pwdRules .pw-rule').forEach(row => {
            const pass = rules[row.dataset.rule](v);
            const ico  = row.querySelector('i');
            ico.className = pass ? 'fa fa-check-circle me-1' : 'fa fa-times-circle me-1';
            ico.style.color = pass ? 'var(--accent-gold)' : '#c07070';
        });
        checkConfirm();
    });

    cfmInput.addEventListener('input', checkConfirm);

    function checkConfirm() {
        if (!cfmInput.value) { confirmHint.style.display = 'none'; return; }
        confirmHint.style.display = '';
        if (pwdInput.value === cfmInput.value) {
            confirmMsg.style.color = 'var(--accent-gold)';
            confirmMsg.textContent = '✓ 密碼一致';
        } else {
            confirmMsg.style.color = '#c07070';
            confirmMsg.textContent = '✗ 密碼不一致';
        }
    }

    toggleBtn.addEventListener('click', function () {
        const show = pwdInput.type === 'password';
        pwdInput.type = show ? 'text' : 'password';
        toggleIcon.className = show ? 'fa fa-eye-slash' : 'fa fa-eye';
    });

    cfmBtn.addEventListener('click', function () {
        const show = cfmInput.type === 'password';
        cfmInput.type = show ? 'text' : 'password';
        cfmIcon.className = show ? 'fa fa-eye-slash' : 'fa fa-eye';
    });
})();
</script>
@endsection
