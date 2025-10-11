@extends('admin.layouts.app')

@section('content')
    <h2>輪播圖管理</h2>
    <a href="{{ route('admin.carousel.create') }}" class="btn btn-success mb-3">新增輪播圖</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>標題</th>
                <th>圖片</th>
                <th>排序</th>
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
                    <td>{{ $carousel->order_num }}</td>
                    <td>
                        <form action="{{ route('admin.carousel.toggle', $carousel->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm {{ $carousel->is_active ? 'btn-success' : 'btn-secondary' }}">
                                {{ $carousel->is_active ? '顯示' : '隱藏' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('admin.carousel.edit', $carousel->id) }}" class="btn btn-sm btn-primary">編輯</a>

                        {{-- 上移/下移 --}}
                        <a href=" {{ route('admin.carousel.swap', ['id' => $carousel->id, 'direction' => 'up']) }}"
                            class="btn btn-sm btn-secondary">上移</a>
                        <a href=" {{ route('admin.carousel.swap', ['id' => $carousel->id, 'direction' => 'down']) }}"
                            class="btn btn-sm btn-secondary">下移</a>

                        <form action="{{ route('admin.carousel.destroy', $carousel->id) }}" method="POST"
                            style="display:inline;">
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
