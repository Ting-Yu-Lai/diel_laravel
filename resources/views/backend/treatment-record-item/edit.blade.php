@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">編輯療程明細</h1>
    <a href="{{ route('backend.treatment-record.show', $record->id) }}" class="btn btn-secondary">返回</a>
</div>

<p class="text-muted mb-3">
    療程紀錄：<strong>{{ $record->customer->name ?? '—' }}</strong>
    &nbsp;／&nbsp;{{ $record->record_date->format('Y-m-d') }}
</p>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form id="treatment-record-item-edit-form"
      action="{{ route('backend.treatment-record-item.update', [$record->id, $item->id]) }}"
      method="POST"
      style="max-width: 600px;">
    @csrf
    @method('PUT')

    {{-- 療程種類（靜態下拉，不送出，僅用於驅動項目下拉） --}}
    <div class="mb-3">
        <label class="form-label">療程種類 <span class="text-danger">*</span></label>
        <select id="category-select" class="form-select">
            <option value="">— 請選擇療程種類 —</option>
            @foreach ($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ $cat->id === $selectedCategoryId ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- 療程項目（依種類動態載入，並預選） --}}
    <div class="mb-3">
        <label class="form-label">療程項目 <span class="text-danger">*</span></label>
        <select id="treatment-select" name="treatment_id" class="form-select" required>
            <option value="{{ $item->treatment_id }}">{{ $item->treatment->name }}</option>
        </select>
    </div>

    {{-- 售價 --}}
    <div class="mb-3">
        <label class="form-label">售價（NT$）<span class="text-danger">*</span></label>
        <input type="number" name="price" class="form-control"
               value="{{ old('price', $item->price) }}" min="0" required>
    </div>

    {{-- 成本 --}}
    <div class="mb-3">
        <label class="form-label">成本（NT$）<span class="text-danger">*</span></label>
        <input type="number" name="cost" class="form-control"
               value="{{ old('cost', $item->cost) }}" min="0" required>
    </div>

    {{-- 負責醫師（單選 Autocomplete） --}}
    <div class="mb-3">
        <label class="form-label">負責醫師</label>
        <div class="position-relative">
            <input type="text"
                   id="doctor-search-text"
                   class="form-control"
                   placeholder="輸入姓名搜尋..."
                   autocomplete="off"
                   value="{{ $item->staff?->name ?? '' }}">
            <input type="hidden"
                   id="doctor-id-hidden"
                   name="staff_id"
                   value="{{ old('staff_id', $item->staff_id) }}">
            <div id="doctor-search-results"
                 class="list-group position-absolute w-100 shadow-sm"
                 style="z-index:1050; display:none; max-height:220px; overflow-y:auto; top:100%;"></div>
        </div>
        <div class="form-text">可留空</div>
    </div>

    {{-- 備註 --}}
    <div class="mb-3">
        <label class="form-label">細節備註</label>
        <textarea name="notes" class="form-control" rows="3"
                  maxlength="1000">{{ old('notes', $item->notes) }}</textarea>
    </div>

    <button type="submit" class="btn btn-success">
        <i class="fa-solid fa-floppy-disk"></i> 儲存
    </button>
    <a href="{{ route('backend.treatment-record.show', $record->id) }}" class="btn btn-secondary ms-2">取消</a>
</form>

<script>
(function () {
    const byCategoryUrl  = '{{ route('backend.treatment.byCategoryJson') }}';
    const staffSearchUrl = '{{ route('backend.staff.searchJson') }}';

    /* ========== 療程種類 → 療程項目 級聯下拉 ========== */
    const categorySelect      = document.getElementById('category-select');
    const treatmentSelect     = document.getElementById('treatment-select');
    const preselectedCategory = {{ $selectedCategoryId ?? 'null' }};
    const preselectedTreatment = {{ $item->treatment_id }};

    function loadTreatments(categoryId, selectAfterLoad) {
        treatmentSelect.innerHTML = '<option value="">載入中...</option>';
        treatmentSelect.disabled = true;

        fetch(byCategoryUrl + '?category_id=' + categoryId)
            .then(r => r.json())
            .then(treatments => {
                treatmentSelect.innerHTML = '<option value="">— 請選擇療程項目 —</option>';
                if (!treatments.length) {
                    treatmentSelect.innerHTML = '<option value="">此分類目前無啟用療程</option>';
                } else {
                    treatments.forEach(t => {
                        const opt = document.createElement('option');
                        opt.value = t.id;
                        opt.textContent = t.name;
                        if (selectAfterLoad && t.id === selectAfterLoad) opt.selected = true;
                        treatmentSelect.appendChild(opt);
                    });
                    treatmentSelect.disabled = false;
                }
            });
    }

    // 頁面載入時自動帶入已選種類的療程清單
    if (preselectedCategory) {
        loadTreatments(preselectedCategory, preselectedTreatment);
    }

    categorySelect.addEventListener('change', function () {
        const categoryId = this.value;
        if (!categoryId) {
            treatmentSelect.innerHTML = '<option value="">— 請先選擇療程種類 —</option>';
            treatmentSelect.disabled = true;
            return;
        }
        loadTreatments(categoryId, null);
    });

    /* ========== 負責醫師 Autocomplete（單選） ========== */
    const doctorTextInput  = document.getElementById('doctor-search-text');
    const doctorIdHidden   = document.getElementById('doctor-id-hidden');
    const doctorResultList = document.getElementById('doctor-search-results');
    let doctorDebounce;

    function closeDoctorResults() {
        doctorResultList.innerHTML = '';
        doctorResultList.style.display = 'none';
    }

    doctorTextInput.addEventListener('focus', function () {
        fetchDoctors(this.value.trim());
    });

    doctorTextInput.addEventListener('input', function () {
        clearTimeout(doctorDebounce);
        doctorIdHidden.value = '';
        const keyword = this.value.trim();
        if (!keyword) { closeDoctorResults(); return; }

        doctorDebounce = setTimeout(() => fetchDoctors(keyword), 280);
    });

    function fetchDoctors(keyword) {
        fetch(staffSearchUrl + '?role=doctor&q=' + encodeURIComponent(keyword))
            .then(r => r.json())
            .then(staffList => {
                doctorResultList.innerHTML = '';
                if (!staffList.length) {
                    doctorResultList.innerHTML = '<div class="list-group-item text-muted small py-2">查無符合醫師</div>';
                } else {
                    staffList.forEach(s => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'list-group-item list-group-item-action py-2';
                        btn.textContent = s.name;
                        btn.addEventListener('click', () => {
                            doctorTextInput.value = s.name;
                            doctorIdHidden.value  = s.id;
                            closeDoctorResults();
                        });
                        doctorResultList.appendChild(btn);
                    });
                }
                doctorResultList.style.display = 'block';
            });
    }

    document.addEventListener('click', e => {
        if (!doctorTextInput.contains(e.target) && !doctorResultList.contains(e.target)) {
            closeDoctorResults();
        }
    });

    doctorTextInput.addEventListener('change', function () {
        if (!this.value.trim()) doctorIdHidden.value = '';
    });
})();
</script>
@endsection
