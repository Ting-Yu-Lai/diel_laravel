<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

trait RespondsWithFlash
{
    protected function redirectSuccess(string $route, string $message, array $params = []): RedirectResponse
    {
        return redirect()->route($route, $params)->with('success', $message);
    }

    protected function redirectError(string $message): RedirectResponse
    {
        return back()->with('error', $message);
    }

    protected function jsonSuccess(array $data = [], int $status = 200): JsonResponse
    {
        return response()->json($data, $status);
    }

    protected function jsonError(string $message, int $status = 422): JsonResponse
    {
        return response()->json(['message' => $message], $status);
    }
}
