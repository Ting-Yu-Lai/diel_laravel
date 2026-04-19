<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreTagCategoryRequest;
use App\Http\Requests\Admin\UpdateTagCategoryRequest;
use App\Services\TagCategoryService;

class TagCategoryController extends Controller
{
    public function __construct(
        private readonly TagCategoryService $tagCategoryService,
    ) {}

    public function index()
    {
        $categories = $this->tagCategoryService->allWithTags();
        return view('backend.tag-category.index', compact('categories'));
    }

    public function create()
    {
        return view('backend.tag-category.create');
    }

    public function store(StoreTagCategoryRequest $request)
    {
        $this->tagCategoryService->create($request->validated());
        return redirect()->route('backend.tag-category.index')
            ->with('success', '標籤分類已新增');
    }

    public function edit(int $id)
    {
        $category = $this->tagCategoryService->findById($id);
        return view('backend.tag-category.edit', compact('category'));
    }

    public function update(UpdateTagCategoryRequest $request, int $id)
    {
        $this->tagCategoryService->update($id, $request->validated());
        return redirect()->route('backend.tag-category.index')
            ->with('success', '分類名稱已更新');
    }

    public function destroy(int $id)
    {
        $category = $this->tagCategoryService->findById($id);

        if ($category->tags()->count() > 0) {
            return back()->with('error', '請先刪除此分類底下的所有標籤，再刪除分類');
        }

        $this->tagCategoryService->delete($id);
        return redirect()->route('backend.tag-category.index')
            ->with('success', '標籤分類已刪除');
    }
}
