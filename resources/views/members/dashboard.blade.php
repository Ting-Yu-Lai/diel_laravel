@extends('layouts.app')

@section('title', '會員中心')

@section('content')
    <h1>歡迎，{{ auth('member')->user()->username }}！</h1>
    <p>這裡是會員中心頁面。</p>
@endsection