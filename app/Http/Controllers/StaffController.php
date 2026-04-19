<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreStaffRequest;
use App\Http\Requests\Admin\UpdateStaffRequest;
use App\Models\JobTitle;
use App\Models\StaffDeleteLog;
use App\Services\StaffService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StaffController extends Controller
{
    public function __construct(
        private readonly StaffService $staffService,
    ) {}

    public function index(Request $request)
    {
        if ($request->filled('q')) {
            $staffList = $this->staffService->searchByName($request->q);
        } elseif ($request->filled('job_title_id')) {
            $staffList = $this->staffService->filterByJobTitle((int) $request->job_title_id);
        } else {
            $staffList = $this->staffService->getAll();
        }

        $jobTitles = JobTitle::all();

        return view('backend.staff.index', compact('staffList', 'jobTitles'));
    }

    public function create()
    {
        $jobTitles = JobTitle::all();
        return view('backend.staff.create', compact('jobTitles'));
    }

    public function store(StoreStaffRequest $request)
    {
        $staff = $this->staffService->create($request->validated());
        return response()->json([
            'message' => '工作人員新增成功',
            'id'      => $staff->id,
        ], 201);
    }

    public function show(int $id)
    {
        $staff = $this->staffService->findById($id);
        $staff->load('jobTitle');
        $deleteLogs = $staff->deleteLogs()->with('admin')->orderByDesc('created_at')->get();
        return view('backend.staff.show', compact('staff', 'deleteLogs'));
    }

    public function edit(int $id)
    {
        $staff     = $this->staffService->findById($id);
        $jobTitles = JobTitle::all();
        return view('backend.staff.edit', compact('staff', 'jobTitles'));
    }

    public function update(UpdateStaffRequest $request, int $id)
    {
        $this->staffService->update($id, $request->validated());
        return response()->json(['message' => '工作人員資料已更新']);
    }

    public function destroy(Request $request, int $id)
    {
        if (Session::get('power') != 1) {
            return back()->with('error', '你沒有權限執行此操作');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => '請填寫異動原因',
        ]);

        $staff = $this->staffService->findById($id);

        StaffDeleteLog::create([
            'staff_id'            => $staff->id,
            'staff_name'          => $staff->name,
            'deleted_by_admin_id' => Session::get('admin_id'),
            'reason'              => $request->reason,
        ]);

        $this->staffService->delete($id);

        return redirect()->route('backend.staff.index')
            ->with('success', "工作人員「{$staff->name}」已刪除");
    }
}
