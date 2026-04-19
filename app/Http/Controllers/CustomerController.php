<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\TagCategory;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
    ) {}

    public function index(Request $request)
    {
        if ($request->filled('q')) {
            $customers = $this->customerService->search($request->q);
        } elseif ($request->filled('tag_id')) {
            $customers = $this->customerService->filterByTag((int) $request->tag_id);
        } else {
            $customers = $this->customerService->getAll();
        }

        $tagCategories = TagCategory::with('tags')->get();

        return view('backend.customer.index', compact('customers', 'tagCategories'));
    }

    public function create()
    {
        $tagCategories = TagCategory::with('tags')->get();
        return view('backend.customer.create', compact('tagCategories'));
    }

    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->customerService->create($request->validated());

        return response()->json([
            'message' => '客戶新增成功',
            'id'      => $customer->id,
        ], 201);
    }

    public function show(int $id)
    {
        $customer = $this->customerService->findById($id);
        $customer->load('tags.category');
        return view('backend.customer.show', compact('customer'));
    }

    public function edit(int $id)
    {
        $customer      = $this->customerService->findById($id);
        $tagCategories = TagCategory::with('tags')->get();
        $assignedTagIds = $customer->tags()->pluck('tags.id')->toArray();
        return view('backend.customer.edit', compact('customer', 'tagCategories', 'assignedTagIds'));
    }

    public function update(UpdateCustomerRequest $request, int $id)
    {
        $this->customerService->update($id, $request->validated());

        return response()->json(['message' => '客戶資料已更新']);
    }

    public function syncTags(Request $request, int $id)
    {
        $tagIds = $request->input('tag_ids', []);
        $this->customerService->syncTags($id, $tagIds);
        return response()->json(['message' => '標籤已更新']);
    }

    public function destroy(int $id)
    {
        //
    }
}
