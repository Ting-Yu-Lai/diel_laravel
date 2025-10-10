@extends('admin.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">後台首頁</h1>
</div>

<p>歡迎，{{ session('full_name') ?? session('username') }}！</p>

<div class="row">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fa-solid fa-image fa-2x"></i>
                <h5 class="card-title mt-2">輪播圖管理</h5>
                {{-- <a href="{{ route('admin.carousel') }}" class="btn btn-primary btn-sm">管理</a> --}}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="fa-solid fa-cake-candles fa-2x"></i>
                <h5 class="card-title mt-2">今日甜點管理</h5>
                {{-- <a href="{{ route('admin.dessert') }}" class="btn btn-primary btn-sm">管理</a> --}}
            </div>
        </div>
    </div>
</div>
@endsection
