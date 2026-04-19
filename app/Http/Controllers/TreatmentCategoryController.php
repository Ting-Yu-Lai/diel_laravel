<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreTreatmentCategoryRequest;
use App\Http\Requests\Admin\UpdateTreatmentCategoryRequest;
use App\Models\TreatmentCategoryDeleteLog;
use App\Services\TreatmentCategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class TreatmentCategoryController extends Controller
{
    public function __construct(
        private readonly TreatmentCategoryService $treatmentCategoryService,
    ) {}

    public function index()
    {
        $categories = $this->treatmentCategoryService->allWithTreatments();
        return view('backend.treatment-category.index', compact('categories'));
    }

    public function create()
    {
        return view('backend.treatment-category.create');
    }

    public function store(StoreTreatmentCategoryRequest $request)
    {
        $this->treatmentCategoryService->create($request->validated());
        return redirect()->route('backend.treatment-category.index')
            ->with('success', '療程分類已新增');
    }

    public function edit(int $id)
    {
        $category = $this->treatmentCategoryService->findById($id);
        return view('backend.treatment-category.edit', compact('category'));
    }

    public function update(UpdateTreatmentCategoryRequest $request, int $id)
    {
        $this->treatmentCategoryService->update($id, $request->validated());
        return redirect()->route('backend.treatment-category.index')
            ->with('success', '分類名稱已更新');
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

        $category = $this->treatmentCategoryService->findById($id);

        if ($category->treatments()->count() > 0) {
            return back()->with('error', '請先刪除此分類底下的所有療程項目，再刪除分類');
        }

        TreatmentCategoryDeleteLog::create([
            'category_id'         => $category->id,
            'category_name'       => $category->name,
            'deleted_by_admin_id' => Session::get('admin_id'),
            'reason'              => $request->reason,
        ]);

        $this->treatmentCategoryService->delete($id);

        return redirect()->route('backend.treatment-category.index')
            ->with('success', "療程分類「{$category->name}」已刪除");
    }
}
