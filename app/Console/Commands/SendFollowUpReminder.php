<?php

namespace App\Console\Commands;

use App\Models\FollowUp;
use App\Services\LineReminderService;
use Illuminate\Console\Command;

class SendFollowUpReminder extends Command
{
    protected $signature = 'line:remind
                            {--day=3  : 模擬天數（3、6、7）}
                            {--id=    : 指定 follow_up id（不填則對所有已綁定 LINE 的追蹤發送）}
                            {--list   : 列出所有已綁定 LINE 的追蹤，不發送}';

    protected $description = 'Demo 用：強制模擬指定天數的術後追蹤 LINE 提醒';

    public function __construct(
        private readonly LineReminderService $lineReminderService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $allLineFollowUps = FollowUp::with([
            'treatmentRecordItem.treatment',
            'treatmentRecordItem.treatmentRecord.customer.member',
        ])
        ->whereHas('treatmentRecordItem.treatmentRecord.customer.member',
            fn($q) => $q->whereNotNull('line_user_id')
        )
        ->get();

        if ($this->option('list')) {
            if ($allLineFollowUps->isEmpty()) {
                $this->warn('找不到任何已綁定 LINE 的追蹤紀錄。');
                return Command::SUCCESS;
            }
            $rows = $allLineFollowUps->map(fn($f) => [
                $f->id,
                $f->treatmentRecordItem->treatmentRecord->customer->member->full_name ?? '—',
                $f->treatmentRecordItem->treatment->name ?? '—',
                $f->status,
            ])->toArray();
            $this->table(['ID', '會員', '療程', '狀態'], $rows);
            return Command::SUCCESS;
        }

        $day = (int) $this->option('day');
        $id  = $this->option('id');

        if (! in_array($day, [3, 6, 7], true)) {
            $this->error('--day 只接受 3、6、7');
            return Command::FAILURE;
        }

        if ($id) {
            $followUp = FollowUp::find((int) $id);
            if (! $followUp) {
                $this->error("找不到 follow_up id={$id}");
                return Command::FAILURE;
            }
            $this->lineReminderService->sendReminderForFollowUp($followUp, $day, sync: true);
            $this->info("已發送第 {$day} 天提醒 → follow_up #{$id}");
            return Command::SUCCESS;
        }

        if ($allLineFollowUps->isEmpty()) {
            $this->warn('找不到任何已綁定 LINE 的追蹤紀錄，未發送任何訊息。');
            return Command::SUCCESS;
        }

        foreach ($allLineFollowUps as $followUp) {
            $this->lineReminderService->sendReminderForFollowUp($followUp, $day, sync: true);
            $name = $followUp->treatmentRecordItem->treatmentRecord->customer->member->full_name ?? '?';
            $this->line("  → #{$followUp->id} {$name} ({$followUp->status})");
        }

        $this->info("已發送第 {$day} 天提醒 → {$allLineFollowUps->count()} 筆");
        return Command::SUCCESS;
    }
}
