<?php

namespace App\Jobs;

use App\Services\LineService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendLineQuickReplyJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $lineUserId,
        public readonly string $text,
        public readonly array  $quickReplyItems,
    ) {}

    public function handle(LineService $lineService): void
    {
        $lineService->pushTextWithQuickReply($this->lineUserId, $this->text, $this->quickReplyItems);
    }
}
