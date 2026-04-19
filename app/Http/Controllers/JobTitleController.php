<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreJobTitleRequest;
use App\Http\Requests\Admin\UpdateJobTitleRequest;
use App\Services\JobTitleService;

class JobTitleController extends Controller
{
    public function __construct(
        private readonly JobTitleService $jobTitleService,
    ) {}

    public function index()
    {
        $jobTitles = $this->jobTitleService->getAll();
        return view('backend.job-title.index', compact('jobTitles'));
    }

    public function create()
    {
        return view('backend.job-title.create');
    }

    public function store(StoreJobTitleRequest $request)
    {
        $this->jobTitleService->create($request->validated());
        return redirect()->route('backend.job-title.index')
            ->with('success', '職稱已新增');
    }

    public function edit(int $id)
    {
        $jobTitle = $this->jobTitleService->findById($id);
        return view('backend.job-title.edit', compact('jobTitle'));
    }

    public function update(UpdateJobTitleRequest $request, int $id)
    {
        $this->jobTitleService->update($id, $request->validated());
        return redirect()->route('backend.job-title.index')
            ->with('success', '職稱已更新');
    }

    public function destroy(int $id)
    {
        $jobTitle = $this->jobTitleService->findById($id);

        if ($jobTitle->staff()->count() > 0) {
            return back()->with('error', '請先移除此職稱底下的所有工作人員，再刪除職稱');
        }

        $this->jobTitleService->delete($id);
        return redirect()->route('backend.job-title.index')
            ->with('success', '職稱已刪除');
    }
}
