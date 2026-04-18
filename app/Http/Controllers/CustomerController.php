<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
    ) {}

    public function index(Request $request)
    {
        $customers = $request->filled('q')
            ? $this->customerService->search($request->q)
            : $this->customerService->getAll();

        return view('backend.customer.index', compact('customers'));
    }

    public function create()
    {
        return view('backend.customer.create');
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
        return view('backend.customer.show', compact('customer'));
    }

    public function edit(int $id)
    {
        $customer = $this->customerService->findById($id);
        return view('backend.customer.edit', compact('customer'));
    }

    public function update(UpdateCustomerRequest $request, int $id)
    {
        $this->customerService->update($id, $request->validated());

        return response()->json(['message' => '客戶資料已更新']);
    }

    public function destroy(int $id)
    {
        //
    }
}
