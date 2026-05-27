<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

trait AuthorizesDestroy
{
    protected function authorizeDestroy(): ?RedirectResponse
    {
        if (Session::get('power') < Admin::ROLE_MANAGER) {
            return back()->with('error', '你沒有權限執行此操作');
        }
        return null;
    }

    protected function validateDestroyReason(Request $request, string $verb = '刪除'): void
    {
        $request->validate(
            ['reason' => 'required|string|max:500'],
            ['reason.required' => "請填寫{$verb}原因"],
        );
    }
}
