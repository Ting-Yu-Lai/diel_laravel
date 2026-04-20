<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreTreatmentRequest;
use App\Http\Requests\Admin\UpdateTreatmentRequest;
use App\Models\Treatment;
use App\Models\TreatmentCategory;
use App\Models\TreatmentDeleteLog;
use App\Services\TreatmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TreatmentController extends Controller
{
    public function __construct(
        private readonly TreatmentService $treatmentService,
    ) {}

    /** 依療程分類回傳啟用中的療程列表，供療程記錄明細表單 AJAX 使用 */
    public function byCategoryJson(Request $request)
    {
        $categoryId = (int) $request->get('category_id', 0);

        if (!$categoryId) {
            return response()->json([]);
        }

        $treatments = Treatment::where('treatment_category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($treatments);
    }

    public function index(Request $request)
    {
        if ($request->filled('category_id')) {
            $treatments = $this->treatmentService->filterByCategory((int) $request->category_id);
        } else {
            $treatments = $this->treatmentService->getAll();
        }

        $categories = TreatmentCategory::all();

        return view('backend.treatment.index', compact('treatments', 'categories'));
    }

    public function create()
    {
        $categories = TreatmentCategory::all();
        return view('backend.treatment.create', compact('categories'));
    }

    public function store(StoreTreatmentRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $this->treatmentService->create($data);
        return redirect()->route('backend.treatment.index')
            ->with('success', '療程項目已新增');
    }

    public function edit(int $id)
    {
        $treatment  = $this->treatmentService->findById($id);
        $categories = TreatmentCategory::all();
        return view('backend.treatment.edit', compact('treatment', 'categories'));
    }

    public function update(UpdateTreatmentRequest $request, int $id)
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', false);
        $this->treatmentService->update($id, $data);
        return redirect()->route('backend.treatment.index')
            ->with('success', '療程項目已更新');
    }

    public function toggle(int $id)
    {
        $treatment = $this->treatmentService->toggle($id);
        $status    = $treatment->is_active ? '啟用' : '停用';
        return back()->with('success', "療程「{$treatment->name}」已{$status}");
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

        $treatment = $this->treatmentService->findById($id);

        TreatmentDeleteLog::create([
            'treatment_id'        => $treatment->id,
            'treatment_name'      => $treatment->name,
            'deleted_by_admin_id' => Session::get('admin_id'),
            'reason'              => $request->reason,
        ]);

        $this->treatmentService->delete($id);

        return redirect()->route('backend.treatment.index')
            ->with('success', "療程「{$treatment->name}」已刪除");
    }
}
