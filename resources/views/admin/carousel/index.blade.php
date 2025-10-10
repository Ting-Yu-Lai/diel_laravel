@extends('admin.layouts.app')

@section('content')
<h2>輪播圖管理</h2>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>標題</th>
            <th>圖片</th>
            <th>狀態</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($carousels as $carousel)
        <tr>
            <td>{{ $carousel->id }}</td>
            <td>{{ $carousel->title }}</td>
            <td><img src="{{ asset($carousel->image_url) }}" alt="{{ $carousel->title }}" width="150"></td>
            <td>{{ $carousel->is_active ? '啟用' : '停用' }}</td>
            <td>
                <a href="{{ route('admin.carousel.edit', $carousel->id) }}" class="btn btn-sm btn-primary">編輯</a>
                <form action="{{ route('admin.carousel.destroy', $carousel->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">刪除</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

