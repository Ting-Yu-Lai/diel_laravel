<?php

namespace App\Notifications\Channels;

use App\Services\LineService;
use Illuminate\Notifications\Notification;

class LineChannel
{
    public function __construct(private LineService $lineService) {}

    public function send(mixed $notifiable, Notification $notification): void
    {
        $lineUserId = $notifiable->line_user_id ?? null;
        if (! $lineUserId) {
            return;
        }

        $text = $notification->toLine($notifiable);
        $this->lineService->pushMessage($lineUserId, $text);
    }
}
