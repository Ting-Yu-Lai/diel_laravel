@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">療程分類管理</h1>
    <a href="{{ route('backend.treatment-category.create') }}" class="btn btn-success">
        <i class="fa-solid fa-plus"></i> 新增分類
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($categories->isEmpty())
    <p class="text-muted">尚無療程分類，請點擊「新增分類」開始建立。</p>
@else
    <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>分類名稱</th>
                    <th>療程數量</th>
                    <th>建立日期</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->treatments->count() }}</td>
                        <td>{{ $category->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('backend.treatment-category.edit', $category->id) }}"
                                class="btn btn-sm btn-primary">編輯</a>
                            @if (Session::get('power') == 1)
                                <button class="btn btn-sm btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-id="{{ $category->id }}"
                                    data-name="{{ $category->name }}">刪除</button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@if (Session::get('power') == 1)
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">刪除療程分類</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>確定要刪除分類 <strong id="deleteName"></strong>？若分類下仍有療程項目則無法刪除。</p>
                    <div class="mb-3">
                        <label class="form-label">刪除原因 <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3"
                            placeholder="請填寫刪除原因（必填）" required maxlength="500"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-danger">確認刪除</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    const btn = e.relatedTarget;
    const id = btn.getAttribute('data-id');
    const name = btn.getAttribute('data-name');
    document.getElementById('deleteName').textContent = name;
    document.getElementById('deleteForm').action = `/backend/treatment-category/${id}`;
    document.querySelector('#deleteModal textarea[name="reason"]').value = '';
});
</script>
@endif
@endsection
