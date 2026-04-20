<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreTreatmentRecordItemRequest;
use App\Http\Requests\Admin\UpdateTreatmentRecordItemRequest;
use App\Models\Staff;
use App\Models\TreatmentCategory;
use App\Models\TreatmentRecord;
use App\Services\TreatmentRecordItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TreatmentRecordItemController extends Controller
{
    public function __construct(
        private readonly TreatmentRecordItemService $itemService,
    ) {}

    public function create(int $recordId)
    {
        $record     = TreatmentRecord::findOrFail($recordId);
        $categories = TreatmentCategory::orderBy('name')->get();
        $doctors    = Staff::whereHas('jobTitle', fn($q) => $q->where('name', 'like', '%醫師%'))
            ->where('is_active', true)->orderBy('name')->get();

        return view('backend.treatment-record-item.create', compact('record', 'categories', 'doctors'));
    }

    public function store(StoreTreatmentRecordItemRequest $request, int $recordId)
    {
        $this->itemService->create($recordId, $request->only([
            'treatment_id', 'price', 'cost', 'staff_id', 'notes',
        ]));

        return redirect()->route('backend.treatment-record.show', $recordId)
            ->with('success', '療程明細已新增');
    }

    public function edit(int $recordId, int $id)
    {
        $item       = $this->itemService->findById($id);
        $record     = TreatmentRecord::findOrFail($recordId);
        $categories = TreatmentCategory::orderBy('name')->get();
        $doctors    = Staff::whereHas('jobTitle', fn($q) => $q->where('name', 'like', '%醫師%'))
            ->where('is_active', true)->orderBy('name')->get();

        $selectedCategoryId = $item->treatment->treatment_category_id;

        return view('backend.treatment-record-item.edit', compact(
            'item', 'record', 'categories', 'doctors', 'selectedCategoryId'
        ));
    }

    public function update(UpdateTreatmentRecordItemRequest $request, int $recordId, int $id)
    {
        $this->itemService->update($id, $request->only([
            'treatment_id', 'price', 'cost', 'staff_id', 'notes',
        ]));

        return redirect()->route('backend.treatment-record.show', $recordId)
            ->with('success', '療程明細已更新');
    }

    public function destroy(Request $request, int $recordId, int $id)
    {
        if (Session::get('power') != 1) {
            return back()->with('error', '你沒有權限執行此操作');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => '請填寫刪除原因',
        ]);

        $item = $this->itemService->findById($id);

        $this->itemService->delete(
            $id,
            $item->treatment->name,
            $request->reason,
            (int) Session::get('admin_id'),
        );

        return redirect()->route('backend.treatment-record.show', $recordId)
            ->with('success', "療程明細「{$item->treatment->name}」已刪除");
    }
}
