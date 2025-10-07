<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Diel 晝夜咖啡廳</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    {{-- 導覽列 --}}
    @include('front.partials.navbar')

    {{-- 輪播圖 --}}
    @include('front.partials.carousel')

    {{-- 今日甜點 --}}
    @include('front.partials.desserts')

    {{-- 菜單 --}}
    @include('front.partials.menu')

    {{-- 活動剪影 --}}
    @include('front.partials.events')

    {{-- 頁尾 --}}
    @include('front.partials.footer')
</body>
</html>