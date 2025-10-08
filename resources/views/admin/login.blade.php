<!DOCTYPE html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>管理者 - 登入</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        body {
            background-image: url('{{ asset('images/backgrounds/loginbgc.jpg') }}');
            background-size: cover;
            background-position: center;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2 class="text-center mb-4">管理者登入</h2>
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif
        <form action="{{ route('admin.login') }}" method="post">
            @csrf
            <div class="mb-3">
                <label for="acc" class="form-label">帳號</label>
                <input type="text" class="form-control login-form-control" id="acc" name="username" placeholder="輸入帳號"
                    required>
            </div>
            <div class="mb-4">
                <label for="pw" class="form-label">密碼</label>
                <input type="password" class="form-control login-form-control" id="pw" name="password" placeholder="輸入密碼"
                    required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-login">登入</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
