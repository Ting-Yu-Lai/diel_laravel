<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(
        private readonly CustomerService $customerService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $customers = $this->customerService->getAll();

        return response()->json([
            'data' => CustomerResource::collection($customers),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $customer = $this->customerService->findById($id);

        if (!$customer) {
            return response()->json(['message' => '客戶不存在。'], 404);
        }

        return response()->json(new CustomerResource($customer));
    }
}
