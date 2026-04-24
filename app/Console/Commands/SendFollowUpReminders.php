<?php

namespace App\Console\Commands;

use App\Services\LineReminderService;
use Illuminate\Console\Command;

class SendFollowUpReminders extends Command
{
    protected $signature   = 'line:send-followup-reminders';
    protected $description = '每日早上 08:00 發送術後追蹤 LINE 提醒';

    public function __construct(private readonly LineReminderService $lineReminderService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->lineReminderService->sendRemindersToAllOngoing();
        $this->info('Follow-up reminders dispatched.');
        return Command::SUCCESS;
    }
}
