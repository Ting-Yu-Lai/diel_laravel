<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreTreatmentRecordRequest;
use App\Http\Requests\Admin\UpdateTreatmentRecordRequest;
use App\Models\Customer;
use App\Models\Staff;
use App\Models\TreatmentRecordDeleteLog;
use App\Services\TreatmentRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TreatmentRecordController extends Controller
{
    public function __construct(
        private readonly TreatmentRecordService $service,
    ) {}

    public function index(Request $request)
    {
        $records  = $this->service->filter($request->only(['customer_id', 'date_from', 'date_to', 'staff_id']));
        $allStaff = Staff::where('is_active', true)->orderBy('name')->get();

        // 僅載入當前篩選的客戶以顯示名稱，不載入全部客戶列表
        $filteredCustomer = $request->filled('customer_id')
            ? Customer::find($request->customer_id)
            : null;

        return view('backend.treatment-record.index', compact('records', 'allStaff', 'filteredCustomer'));
    }

    public function create()
    {
        $consultants = $this->loadConsultants();

        return view('backend.treatment-record.create', compact('consultants'));
    }

    public function store(StoreTreatmentRecordRequest $request)
    {
        $data        = $request->only(['customer_id', 'record_date', 'notes']);
        $staffByRole = $request->only(['doctor_ids', 'nurse_ids', 'consultant_id']);

        $record = $this->service->create($data, $staffByRole);

        return redirect()->route('backend.treatment-record.show', $record->id)
            ->with('success', '療程紀錄已建立');
    }

    public function show(int $id)
    {
        $record = $this->service->findById($id);
        $record->load('customer', 'doctors', 'nurses', 'consultants', 'items.treatment', 'items.followUp');

        return view('backend.treatment-record.show', compact('record'));
    }

    public function edit(int $id)
    {
        $record = $this->service->findById($id);
        $record->load('customer', 'doctors', 'nurses', 'consultants');

        $consultants = $this->loadConsultants();

        // 預選的醫師與護理師序列化為 JSON，供前端 JS 初始化多選元件
        $preselectedDoctors = $record->doctors->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values();
        $preselectedNurses  = $record->nurses->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values();
        $selectedConsultantId = $record->consultants->first()?->id;

        return view('backend.treatment-record.edit', compact(
            'record', 'consultants',
            'preselectedDoctors', 'preselectedNurses', 'selectedConsultantId'
        ));
    }

    public function update(UpdateTreatmentRecordRequest $request, int $id)
    {
        $data        = $request->only(['customer_id', 'record_date', 'notes']);
        $staffByRole = $request->only(['doctor_ids', 'nurse_ids', 'consultant_id']);

        $this->service->update($id, $data, $staffByRole);

        return redirect()->route('backend.treatment-record.show', $id)
            ->with('success', '療程紀錄已更新');
    }

    public function destroy(Request $request, int $id)
    {
        if (Session::get('power') != 1) {
            return back()->with('error', '你沒有權限執行此操作');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => '請填寫刪除原因',
        ]);

        $record = $this->service->findById($id);

        TreatmentRecordDeleteLog::create([
            'treatment_record_id' => $record->id,
            'customer_id'         => $record->customer_id,
            'record_date'         => $record->record_date,
            'deleted_by_admin_id' => Session::get('admin_id'),
            'reason'              => $request->reason,
        ]);

        $this->service->delete($id);

        return redirect()->route('backend.treatment-record.index')
            ->with('success', "療程紀錄（{$record->record_date->format('Y-m-d')}）已刪除");
    }

    private function loadConsultants()
    {
        return Staff::whereHas('jobTitle', fn($q) => $q->where('name', 'like', '%諮詢師%'))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
