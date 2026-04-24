<?php

namespace App\Http\Controllers;

use App\Repositories\FollowUpRepository;
use App\Repositories\MemberRepository;
use App\Services\LinePhotoService;
use App\Services\LineReminderService;
use App\Services\LineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LineController extends Controller
{
    public function __construct(
        private LineService         $lineService,
        private MemberRepository    $memberRepository,
        private FollowUpRepository  $followUpRepository,
        private LineReminderService $lineReminderService,
        private LinePhotoService    $linePhotoService,
    ) {}

    public function oauthRedirect(): RedirectResponse
    {
        $state = Str::random(32);
        session(['line_oauth_state' => $state]);

        return redirect($this->lineService->buildOAuthUrl($state));
    }

    public function oauthCallback(Request $request): RedirectResponse
    {
        if ($request->input('state') !== session('line_oauth_state')) {
            return redirect()->route('member.profile')
                ->with('error', 'LINE 綁定失敗：state 驗證錯誤，請重試。');
        }

        $code = $request->input('code');
        if (! $code) {
            return redirect()->route('member.profile')
                ->with('error', 'LINE 授權失敗，請重試。');
        }

        try {
            $lineUserId = $this->lineService->exchangeCodeForUserId($code);
        } catch (\Throwable $e) {
            Log::error('LINE OAuth callback error', ['error' => $e->getMessage()]);
            return redirect()->route('member.profile')
                ->with('error', 'LINE 綁定失敗，請稍後再試。');
        }

        $memberId = Auth::guard('member')->id();
        $this->memberRepository->setLineUserId($memberId, $lineUserId);

        session()->forget('line_oauth_state');

        return redirect()->route('member.profile')
            ->with('success', 'LINE 帳號綁定成功！');
    }

    public function unbind(Request $request): RedirectResponse
    {
        $memberId = Auth::guard('member')->id();
        $this->memberRepository->setLineUserId($memberId, null);

        return redirect()->route('member.profile')
            ->with('success', 'LINE 帳號已解除綁定。');
    }

    public function webhook(Request $request): Response
    {
        $rawBody   = $request->getContent();
        $signature = $request->header('X-Line-Signature', '');

        if (! $this->lineService->verifyWebhookSignature($rawBody, $signature)) {
            abort(403, 'Invalid LINE signature');
        }

        $events = $request->input('events', []);

        foreach ($events as $event) {
            $type       = $event['type'] ?? 'unknown';
            $lineUserId = $event['source']['userId'] ?? null;

            Log::info('LINE webhook event', ['type' => $type, 'userId' => $lineUserId]);

            match ($type) {
                'follow'   => $this->handleFollow($lineUserId),
                'unfollow' => $this->handleUnfollow($lineUserId),
                'message'  => $this->handleMessage($event, $lineUserId),
                default    => null,
            };
        }

        return response('OK', 200);
    }

    public function remind(Request $request): JsonResponse
    {
        $request->validate(['member_id' => 'required|integer']);

        $member = $this->memberRepository->find($request->integer('member_id'));

        if (! $member) {
            return response()->json(['success' => false, 'message' => '找不到此會員'], 404);
        }
        if (! $member->line_user_id) {
            return response()->json(['success' => false, 'message' => '此會員尚未綁定 LINE'], 422);
        }
        if (! $this->followUpRepository->findLatestOngoingByMemberId($member->id)) {
            return response()->json(['success' => false, 'message' => '此會員目前沒有進行中的追蹤項目'], 422);
        }

        $this->lineReminderService->sendReminderToMember($member);

        return response()->json(['success' => true, 'message' => '提醒訊息已發送']);
    }

    private function handleFollow(?string $lineUserId): void
    {
        Log::info('LINE follow event', ['userId' => $lineUserId]);
    }

    private function handleUnfollow(?string $lineUserId): void
    {
        Log::info('LINE unfollow event', ['userId' => $lineUserId]);
    }

    private function handleMessage(array $event, ?string $lineUserId): void
    {
        if (($event['message']['type'] ?? '') !== 'image' || ! $lineUserId) {
            return;
        }

        $messageId  = $event['message']['id'];
        $replyToken = $event['replyToken'] ?? null;

        try {
            $replyText = $this->linePhotoService->handleIncomingPhoto($lineUserId, $messageId);
            if ($replyText && $replyToken) {
                $this->lineService->replyMessage($replyToken, $replyText);
            }
        } catch (\Throwable $e) {
            Log::error('LINE photo handling failed', [
                'userId'    => $lineUserId,
                'messageId' => $messageId,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
