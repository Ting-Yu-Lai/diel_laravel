@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">編輯療程紀錄</h1>
    <a href="{{ route('backend.treatment-record.show', $record->id) }}" class="btn btn-secondary">返回</a>
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

<form id="treatment-record-edit-form"
      action="{{ route('backend.treatment-record.update', $record->id) }}"
      method="POST"
      style="max-width: 640px;">
    @csrf
    @method('PUT')

    {{-- 客戶搜尋 --}}
    <div class="mb-3">
        <label class="form-label">客戶 <span class="text-danger">*</span></label>
        <div class="position-relative">
            <input type="text"
                   id="customer-search-text"
                   class="form-control"
                   placeholder="輸入姓名或電話搜尋..."
                   autocomplete="off"
                   value="{{ $record->customer?->name ?? '' }}">
            <input type="hidden"
                   id="customer-id-hidden"
                   name="customer_id"
                   value="{{ old('customer_id', $record->customer_id) }}">
            <div id="customer-search-results"
                 class="list-group position-absolute w-100 shadow-sm"
                 style="z-index:1050; display:none; max-height:240px; overflow-y:auto; top:100%;"></div>
        </div>
        <div class="form-text">請輸入姓名或電話，從結果中選擇</div>
    </div>

    <div class="mb-3">
        <label class="form-label">來診日期 <span class="text-danger">*</span></label>
        <input type="date" name="record_date" class="form-control"
            value="{{ old('record_date', $record->record_date->format('Y-m-d')) }}" required>
    </div>

    <div class="mb-3">
        <label class="form-label">備註</label>
        <textarea name="notes" class="form-control" rows="3" maxlength="1000">{{ old('notes', $record->notes) }}</textarea>
    </div>

    <hr>
    <p class="text-muted small mb-3">
        <i class="fa-solid fa-circle-info"></i>
        療程金額與成本將在療程明細中逐項記錄並自動彙總至此筆紀錄。
    </p>

    {{-- 醫師（API 多選） --}}
    <div class="mb-3">
        <label class="form-label">醫師 <span class="text-muted small fw-normal">（可多選）</span></label>
        <div id="doctor-selected-tags" class="d-flex flex-wrap gap-1 mb-2 min-height-tag"></div>
        <div class="position-relative">
            <input type="text"
                   id="doctor-search-text"
                   class="form-control"
                   placeholder="輸入姓名搜尋，或點擊展開清單..."
                   autocomplete="off">
            <div id="doctor-search-results"
                 class="list-group position-absolute w-100 shadow-sm"
                 style="z-index:1040; display:none; max-height:220px; overflow-y:auto; top:100%;"></div>
        </div>
        <div id="doctor-hidden-inputs"></div>
    </div>

    {{-- 護理師（API 多選） --}}
    <div class="mb-3">
        <label class="form-label">護理師 <span class="text-muted small fw-normal">（可多選）</span></label>
        <div id="nurse-selected-tags" class="d-flex flex-wrap gap-1 mb-2 min-height-tag"></div>
        <div class="position-relative">
            <input type="text"
                   id="nurse-search-text"
                   class="form-control"
                   placeholder="輸入姓名搜尋，或點擊展開清單..."
                   autocomplete="off">
            <div id="nurse-search-results"
                 class="list-group position-absolute w-100 shadow-sm"
                 style="z-index:1040; display:none; max-height:220px; overflow-y:auto; top:100%;"></div>
        </div>
        <div id="nurse-hidden-inputs"></div>
    </div>

    {{-- 諮詢師（單選） --}}
    <div class="mb-3">
        <label class="form-label">諮詢師 <span class="text-muted small fw-normal">（單選）</span></label>
        @if ($consultants->isEmpty())
            <div class="text-muted small">尚無符合職稱的諮詢師</div>
        @else
            <select name="consultant_id" class="form-select">
                <option value="">— 不指定 —</option>
                @foreach ($consultants as $consultant)
                    <option value="{{ $consultant->id }}"
                        {{ old('consultant_id', $selectedConsultantId) == $consultant->id ? 'selected' : '' }}>
                        {{ $consultant->name }}
                    </option>
                @endforeach
            </select>
        @endif
    </div>

    <button type="submit" class="btn btn-success">
        <i class="fa-solid fa-floppy-disk"></i> 儲存
    </button>
    <a href="{{ route('backend.treatment-record.show', $record->id) }}" class="btn btn-secondary ms-2">取消</a>
</form>

<script>
(function () {
    /* ========== 客戶搜尋 ========== */
    const customerSearchUrl  = '{{ route('backend.customer.searchJson') }}';
    const customerTextInput  = document.getElementById('customer-search-text');
    const customerIdHidden   = document.getElementById('customer-id-hidden');
    const customerResultList = document.getElementById('customer-search-results');
    const editForm           = document.getElementById('treatment-record-edit-form');
    let customerDebounce;

    function closeCustomerResults() {
        customerResultList.innerHTML = '';
        customerResultList.style.display = 'none';
    }

    customerTextInput.addEventListener('input', function () {
        clearTimeout(customerDebounce);
        customerIdHidden.value = '';
        const keyword = this.value.trim();
        if (!keyword) { closeCustomerResults(); return; }

        customerDebounce = setTimeout(() => {
            fetch(customerSearchUrl + '?q=' + encodeURIComponent(keyword))
                .then(r => r.json())
                .then(customers => {
                    customerResultList.innerHTML = '';
                    if (!customers.length) {
                        customerResultList.innerHTML = '<div class="list-group-item text-muted small py-2">查無符合客戶</div>';
                    } else {
                        customers.forEach(c => {
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'list-group-item list-group-item-action py-2';
                            btn.textContent = c.name + (c.phone ? '　' + c.phone : '');
                            btn.addEventListener('click', () => {
                                customerTextInput.value  = c.name;
                                customerIdHidden.value   = c.id;
                                customerTextInput.setCustomValidity('');
                                closeCustomerResults();
                            });
                            customerResultList.appendChild(btn);
                        });
                    }
                    customerResultList.style.display = 'block';
                });
        }, 280);
    });

    document.addEventListener('click', e => {
        if (!customerTextInput.contains(e.target) && !customerResultList.contains(e.target)) {
            closeCustomerResults();
        }
    });

    editForm.addEventListener('submit', e => {
        if (!customerIdHidden.value) {
            customerTextInput.setCustomValidity('請搜尋並選擇一位客戶');
            customerTextInput.reportValidity();
            e.preventDefault();
        }
    });
    customerTextInput.addEventListener('input', () => customerTextInput.setCustomValidity(''));

    /* ========== 醫師 / 護理師 API 多選元件 ========== */
    const staffSearchUrl = '{{ route('backend.staff.searchJson') }}';

    /**
     * 初始化一個「API 多選」元件
     * @param {string} role          - 'doctor' 或 'nurse'
     * @param {string} inputName     - 表單欄位名稱，e.g. 'doctor_ids[]'
     * @param {string} textInputId   - 搜尋文字框 id
     * @param {string} resultsId     - 搜尋結果清單 id
     * @param {string} tagsId        - 已選標籤容器 id
     * @param {string} hiddenInputsId- 隱藏欄位容器 id
     * @param {Array}  preselected   - 預選人員 [{id, name}]
     */
    function initStaffMultiSelect({ role, inputName, textInputId, resultsId, tagsId, hiddenInputsId, preselected = [] }) {
        const textInput       = document.getElementById(textInputId);
        const resultList      = document.getElementById(resultsId);
        const tagsContainer   = document.getElementById(tagsId);
        const hiddenContainer = document.getElementById(hiddenInputsId);
        let selectedStaff = [...preselected];
        let debounceTimer;

        function renderTags() {
            tagsContainer.innerHTML = '';
            hiddenContainer.innerHTML = '';
            selectedStaff.forEach(staff => {
                const tag = document.createElement('span');
                tag.className = 'badge bg-primary d-inline-flex align-items-center gap-1 py-1 px-2';
                tag.style.fontSize = '0.875rem';
                tag.innerHTML = `${staff.name}&nbsp;<button type="button" class="btn-close btn-close-white" style="font-size:0.55em;" aria-label="移除"></button>`;
                tag.querySelector('button').addEventListener('click', () => {
                    selectedStaff = selectedStaff.filter(s => s.id !== staff.id);
                    renderTags();
                    if (resultList.style.display === 'block') fetchAndRender(textInput.value.trim());
                });
                tagsContainer.appendChild(tag);

                const hidden = document.createElement('input');
                hidden.type  = 'hidden';
                hidden.name  = inputName;
                hidden.value = staff.id;
                hiddenContainer.appendChild(hidden);
            });
        }

        function renderResults(staffList) {
            resultList.innerHTML = '';
            if (!staffList.length) {
                resultList.innerHTML = '<div class="list-group-item text-muted small py-2">查無符合人員</div>';
                resultList.style.display = 'block';
                return;
            }
            staffList.forEach(staff => {
                const isSelected = selectedStaff.some(s => s.id === staff.id);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'list-group-item list-group-item-action py-2 d-flex justify-content-between align-items-center';
                btn.innerHTML = staff.name + (isSelected
                    ? ' <span class="badge bg-primary">已選</span>'
                    : '');
                btn.addEventListener('click', () => {
                    if (isSelected) {
                        selectedStaff = selectedStaff.filter(s => s.id !== staff.id);
                    } else {
                        selectedStaff.push({ id: staff.id, name: staff.name });
                    }
                    renderTags();
                    fetchAndRender(textInput.value.trim());
                });
                resultList.appendChild(btn);
            });
            resultList.style.display = 'block';
        }

        function fetchAndRender(keyword) {
            fetch(staffSearchUrl + '?role=' + role + '&q=' + encodeURIComponent(keyword))
                .then(r => r.json())
                .then(renderResults);
        }

        function closeResults() {
            resultList.innerHTML = '';
            resultList.style.display = 'none';
        }

        textInput.addEventListener('focus', () => fetchAndRender(textInput.value.trim()));

        textInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => fetchAndRender(this.value.trim()), 280);
        });

        document.addEventListener('click', e => {
            if (!textInput.contains(e.target) && !resultList.contains(e.target)) {
                closeResults();
            }
        });

        renderTags();
    }

    // 由後端傳入已存在的預選人員
    const preselectedDoctors = @json($preselectedDoctors);
    const preselectedNurses  = @json($preselectedNurses);

    initStaffMultiSelect({
        role:           'doctor',
        inputName:      'doctor_ids[]',
        textInputId:    'doctor-search-text',
        resultsId:      'doctor-search-results',
        tagsId:         'doctor-selected-tags',
        hiddenInputsId: 'doctor-hidden-inputs',
        preselected:    preselectedDoctors,
    });

    initStaffMultiSelect({
        role:           'nurse',
        inputName:      'nurse_ids[]',
        textInputId:    'nurse-search-text',
        resultsId:      'nurse-search-results',
        tagsId:         'nurse-selected-tags',
        hiddenInputsId: 'nurse-hidden-inputs',
        preselected:    preselectedNurses,
    });
})();
</script>
@endsection
