<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use App\Models\TagCategory;
use App\Services\TagService;

class TagController extends Controller
{
    public function __construct(
        private readonly TagService $tagService,
    ) {}

    public function store(StoreTagRequest $request, int $categoryId)
    {
        $this->tagService->create([
            'tag_category_id' => $categoryId,
            'name'            => $request->validated()['name'],
        ]);

        return redirect()->route('backend.tag-category.index')
            ->with('success', '標籤已新增');
    }

    public function edit(int $id)
    {
        $tag        = $this->tagService->findById($id);
        $categories = TagCategory::all();
        return view('backend.tag.edit', compact('tag', 'categories'));
    }

    public function update(UpdateTagRequest $request, int $id)
    {
        $this->tagService->update($id, $request->validated());
        return redirect()->route('backend.tag-category.index')
            ->with('success', '標籤已更新');
    }

    public function destroy(int $id)
    {
        $this->tagService->delete($id);
        return redirect()->route('backend.tag-category.index')
            ->with('success', '標籤已刪除');
    }
}
