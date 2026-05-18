<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\CustomerDeleteLog;
use App\Models\TagCategory;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
        $result   = $this->customerService->create($request->validated());
        $customer = $result['customer'];
        $status   = $result['member_status'];
        $password = $result['initial_password'];

        $message = match ($status) {
            'created' => "客戶新增成功，已自動建立會員帳號（初始密碼：{$password}）",
            'linked'  => '客戶新增成功，已自動關聯現有會員帳號',
            default   => '客戶新增成功',
        };

        return response()->json([
            'message'          => $message,
            'id'               => $customer->id,
            'initial_password' => $password,
        ], 201);
    }

    public function show(int $id)
    {
        $customer = $this->customerService->findById($id);
        $customer->load('tags.category', 'member');
        return view('backend.customer.show', compact('customer'));
    }

    public function storeMember(int $id)
    {
        try {
            $result   = $this->customerService->createMemberForCustomer($id);
            $password = $result['initial_password'];
            $message  = $result['member_status'] === 'created'
                ? "會員帳號建立成功（初始密碼：{$password}）"
                : '已關聯現有會員帳號';
            return response()->json([
                'message'        => $message,
                'member_status'  => $result['member_status'],
                'initial_password' => $password,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function resetMemberPassword(int $id)
    {
        try {
            $password = $this->customerService->resetMemberPassword($id);
            return response()->json(['message' => '密碼已重設', 'new_password' => $password]);
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function unlinkMember(int $id)
    {
        $customer = $this->customerService->findById($id);
        if (! $customer?->member_id) {
            return back()->with('error', '此客戶尚未關聯會員帳號');
        }
        $this->customerService->update($id, ['member_id' => null]);
        return redirect()->route('backend.customer.show', $id)->with('success', '已解除會員帳號關聯');
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

    public function searchJson(Request $request)
    {
        $keyword = trim($request->get('q', ''));

        if ($keyword === '') {
            return response()->json([]);
        }

        $digits = preg_replace('/\D/', '', $keyword);

        $customers = Customer::where('is_active', true)
            ->where(function ($query) use ($keyword, $digits) {
                $query->where('name', 'like', "%{$keyword}%");
                if ($digits !== '') {
                    $query->orWhere('phone', 'like', "%{$digits}%");
                }
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'phone']);

        return response()->json($customers->map(fn($c) => [
            'id'    => $c->id,
            'name'  => $c->name,
            'phone' => $c->formatted_phone,
        ]));
    }

    public function destroy(Request $request, int $id)
    {
        if (Session::get('power') < Admin::ROLE_MANAGER) {
            return back()->with('error', '你沒有權限執行此操作');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => '請填寫刪除原因',
        ]);

        $customer = $this->customerService->findById($id);

        CustomerDeleteLog::create([
            'customer_id'         => $customer->id,
            'customer_name'       => $customer->name,
            'deleted_by_admin_id' => Session::get('admin_id'),
            'reason'              => $request->reason,
        ]);

        $this->customerService->delete($id);

        return redirect()->route('backend.customer.index')
            ->with('success', "客戶「{$customer->name}」已刪除");
    }
}
