<?php

namespace App\Jobs;

use App\Services\LineService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendLineFlexMessageJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $lineUserId,
        public readonly array  $flexContents,
    ) {}

    public function handle(LineService $lineService): void
    {
        $lineService->pushFlexMessage($this->lineUserId, $this->flexContents);
    }
}
