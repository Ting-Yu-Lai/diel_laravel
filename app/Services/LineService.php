<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LineService
{
    private string $channelSecret;
    private string $channelAccessToken;
    private string $loginChannelId;
    private string $loginChannelSecret;
    private string $loginRedirectUri;

    public function __construct()
    {
        $this->channelSecret       = config('services.line.channel_secret', '');
        $this->channelAccessToken  = config('services.line.channel_access_token', '');
        $this->loginChannelId      = config('services.line_login.channel_id', '');
        $this->loginChannelSecret  = config('services.line_login.channel_secret', '');
        $this->loginRedirectUri    = config('services.line_login.redirect_uri', '');
    }

    public function buildOAuthUrl(string $state): string
    {
        return 'https://access.line.me/oauth2/v2.1/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id'     => $this->loginChannelId,
            'redirect_uri'  => $this->loginRedirectUri,
            'state'         => $state,
            'scope'         => 'profile openid',
        ]);
    }

    public function exchangeCodeForUserId(string $code): string
    {
        $tokenRes = Http::asForm()->post('https://api.line.me/oauth2/v2.1/token', [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $this->loginRedirectUri,
            'client_id'     => $this->loginChannelId,
            'client_secret' => $this->loginChannelSecret,
        ]);

        $tokenRes->throw();
        $accessToken = $tokenRes->json('access_token');

        $profileRes = Http::withToken($accessToken)
            ->get('https://api.line.me/v2/profile');

        $profileRes->throw();

        return $profileRes->json('userId');
    }

    public function pushMessage(string $lineUserId, string $text): void
    {
        $res = Http::withToken($this->channelAccessToken)
            ->post('https://api.line.me/v2/bot/message/push', [
                'to'       => $lineUserId,
                'messages' => [['type' => 'text', 'text' => $text]],
            ]);

        if ($res->failed()) {
            Log::error('LINE push failed', [
                'userId' => $lineUserId,
                'status' => $res->status(),
                'body'   => $res->body(),
            ]);
        }
    }

    public function pushTextWithQuickReply(string $lineUserId, string $text, array $quickReplyItems): void
    {
        $res = Http::withToken($this->channelAccessToken)
            ->post('https://api.line.me/v2/bot/message/push', [
                'to'       => $lineUserId,
                'messages' => [[
                    'type'       => 'text',
                    'text'       => $text,
                    'quickReply' => ['items' => $quickReplyItems],
                ]],
            ]);

        if ($res->failed()) {
            Log::error('LINE push quick reply failed', [
                'userId' => $lineUserId,
                'status' => $res->status(),
                'body'   => $res->body(),
            ]);
        }
    }

    public function downloadMessageContent(string $messageId): string
    {
        return Http::withToken($this->channelAccessToken)
            ->get("https://api-data.line.me/v2/bot/message/{$messageId}/content")
            ->throw()
            ->body();
    }

    public function replyMessage(string $replyToken, string $text): void
    {
        $res = Http::withToken($this->channelAccessToken)
            ->post('https://api.line.me/v2/bot/message/reply', [
                'replyToken' => $replyToken,
                'messages'   => [['type' => 'text', 'text' => $text]],
            ]);

        if ($res->failed()) {
            Log::error('LINE reply failed', [
                'status' => $res->status(),
                'body'   => $res->body(),
            ]);
        }
    }

    public function verifyWebhookSignature(string $rawBody, string $signature): bool
    {
        $expected = base64_encode(hash_hmac('sha256', $rawBody, $this->channelSecret, true));
        return hash_equals($expected, $signature);
    }
}
