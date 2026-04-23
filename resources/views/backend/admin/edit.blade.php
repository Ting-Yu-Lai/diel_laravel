@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">編輯管理者帳號</h1>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('backend.admin.update', $admin->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label for="username" class="form-label">帳號</label>
        <input type="text" name="username" id="username"
               class="form-control @error('username') is-invalid @enderror"
               value="{{ old('username', $admin->username) }}"
               placeholder="請輸入登入帳號" required>
        @error('username')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="full_name" class="form-label">姓名</label>
        <input type="text" name="full_name" id="full_name"
               class="form-control @error('full_name') is-invalid @enderror"
               value="{{ old('full_name', $admin->full_name) }}"
               placeholder="請輸入真實姓名（選填）">
        @error('full_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">新密碼（留空不更改）</label>
        <div class="input-group">
            <input type="password" name="password" id="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="至少 8 位，含大小寫字母、數字、特殊符號">
            <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                <i class="fa fa-eye" id="toggleIcon"></i>
            </button>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mt-2 ps-1" id="pwdRules" style="display:none;">
            <small class="d-block pw-rule" data-rule="length">
                <i class="fa fa-times-circle text-danger me-1"></i>長度至少 8 個字元
            </small>
            <small class="d-block pw-rule" data-rule="lower">
                <i class="fa fa-times-circle text-danger me-1"></i>包含小寫字母（a-z）
            </small>
            <small class="d-block pw-rule" data-rule="upper">
                <i class="fa fa-times-circle text-danger me-1"></i>包含大寫字母（A-Z）
            </small>
            <small class="d-block pw-rule" data-rule="number">
                <i class="fa fa-times-circle text-danger me-1"></i>包含數字（0-9）
            </small>
            <small class="d-block pw-rule" data-rule="special">
                <i class="fa fa-times-circle text-danger me-1"></i>包含特殊符號（@$!%*#?&^_-）
            </small>
        </div>
        <div class="form-text" id="pwdHint">若不修改密碼請留空</div>
    </div>

    <div class="mb-3">
        <label for="power" class="form-label">權限</label>
        <select name="power" id="power" class="form-select @error('power') is-invalid @enderror">
            <option value="0" {{ old('power', $admin->power) == 0 ? 'selected' : '' }}>一般管理員</option>
            <option value="1" {{ old('power', $admin->power) == 1 ? 'selected' : '' }}>店長</option>
        </select>
        @error('power')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-success">更新</button>
    <a href="{{ route('backend.admin.index') }}" class="btn btn-secondary">返回</a>
</form>

<script>
(function () {
    const input   = document.getElementById('password');
    const toggle  = document.getElementById('togglePassword');
    const icon    = document.getElementById('toggleIcon');
    const rulesEl = document.getElementById('pwdRules');
    const hint    = document.getElementById('pwdHint');
    const rules   = {
        length:  v => v.length >= 8,
        lower:   v => /[a-z]/.test(v),
        upper:   v => /[A-Z]/.test(v),
        number:  v => /[0-9]/.test(v),
        special: v => /[@$!%*#?&^_\-]/.test(v),
    };

    input.addEventListener('input', function () {
        const v = this.value;
        if (v.length > 0) {
            rulesEl.style.display = '';
            hint.style.display    = 'none';
        } else {
            rulesEl.style.display = 'none';
            hint.style.display    = '';
        }
        document.querySelectorAll('#pwdRules .pw-rule').forEach(row => {
            const key  = row.dataset.rule;
            const pass = rules[key](v);
            const ico  = row.querySelector('i');
            ico.className = pass
                ? 'fa fa-check-circle text-success me-1'
                : 'fa fa-times-circle text-danger me-1';
        });
    });

    toggle.addEventListener('click', function () {
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        icon.className = show ? 'fa fa-eye-slash' : 'fa fa-eye';
    });
})();
</script>
@endsection
