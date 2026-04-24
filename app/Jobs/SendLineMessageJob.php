<?php

namespace App\Jobs;

use App\Services\LineService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendLineMessageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $lineUserId,
        public readonly string $text,
    ) {}

    public function handle(LineService $lineService): void
    {
        $lineService->pushMessage($this->lineUserId, $this->text);
    }
}
