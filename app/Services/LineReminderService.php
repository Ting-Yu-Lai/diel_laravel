<?php

namespace App\Services;

use App\Jobs\SendLineFlexMessageJob;
use App\Models\FollowUp;
use App\Repositories\FollowUpRepository;

class LineReminderService
{
    public function __construct(
        private readonly FollowUpRepository $followUpRepository,
    ) {}

    public function sendRemindersToAllOngoing(): void
    {
        foreach ($this->followUpRepository->getOngoingWithLineMembers() as $followUp) {
            $member = $followUp->treatmentRecordItem->treatmentRecord->customer->member;
            SendLineFlexMessageJob::dispatch(
                $member->line_user_id,
                $this->buildFlexBubble($followUp)
            );
        }
    }

    public function sendReminderForFollowUp(FollowUp $followUp): void
    {
        $followUp->load([
            'treatmentRecordItem.treatment',
            'treatmentRecordItem.treatmentRecord.customer.member',
        ]);

        $member = $followUp->treatmentRecordItem->treatmentRecord->customer->member;

        SendLineFlexMessageJob::dispatch(
            $member->line_user_id,
            $this->buildFlexBubble($followUp)
        );
    }

    private function buildFlexBubble(FollowUp $followUp): array
    {
        $item      = $followUp->treatmentRecordItem;
        $record    = $item->treatmentRecord;
        $member    = $record->customer->member;
        $dayNumber = $followUp->created_at->startOfDay()->diffInDays(now()->startOfDay()) + 1;
        $dateStr   = $record->record_date->format('Y年m月d日');

        $infoRows = [];

        $infoRows[] = $this->infoRow('療程名稱', $item->treatment->name);

        if (!empty($item->body_part)) {
            $infoRows[] = $this->infoRow('施作部位', $item->body_part);
        }

        $infoRows[] = $this->infoRow('就診日期', $dateStr);
        $infoRows[] = $this->infoRow('今日天數', '第 ' . $dayNumber . ' 天');

        return [
            'type' => 'bubble',
            'header' => [
                'type'            => 'box',
                'layout'          => 'vertical',
                'backgroundColor' => '#1a1a2e',
                'paddingAll'      => '20px',
                'contents'        => [
                    [
                        'type'   => 'text',
                        'text'   => '新美學診所',
                        'color'  => '#d4a373',
                        'size'   => 'xs',
                        'weight' => 'bold',
                    ],
                    [
                        'type'   => 'text',
                        'text'   => '術後恢復追蹤提醒',
                        'color'  => '#ffffff',
                        'size'   => 'lg',
                        'weight' => 'bold',
                        'margin' => 'sm',
                    ],
                ],
            ],
            'body' => [
                'type'     => 'box',
                'layout'   => 'vertical',
                'spacing'  => 'md',
                'contents' => [
                    [
                        'type'   => 'text',
                        'text'   => '親愛的 ' . $member->full_name . ' 貴賓，',
                        'wrap'   => true,
                        'weight' => 'bold',
                        'size'   => 'md',
                        'color'  => '#1a1a2e',
                    ],
                    ['type' => 'separator'],
                    [
                        'type'     => 'box',
                        'layout'   => 'vertical',
                        'spacing'  => 'sm',
                        'contents' => $infoRows,
                    ],
                    ['type' => 'separator'],
                    [
                        'type'  => 'text',
                        'text'  => '請直接在此 LINE 傳送今日恢復期照片，系統將自動記錄並通知醫師。',
                        'wrap'  => true,
                        'color' => '#555555',
                        'size'  => 'sm',
                    ],
                ],
            ],
            'footer' => [
                'type'            => 'box',
                'layout'          => 'vertical',
                'backgroundColor' => '#f8f9fa',
                'paddingAll'      => '16px',
                'contents'        => [
                    [
                        'type'  => 'text',
                        'text'  => '如有任何不適，請立即聯絡診所或前往就醫。',
                        'color' => '#999999',
                        'size'  => 'xs',
                        'wrap'  => true,
                    ],
                ],
            ],
        ];
    }

    private function infoRow(string $label, string $value): array
    {
        return [
            'type'     => 'box',
            'layout'   => 'horizontal',
            'contents' => [
                [
                    'type'  => 'text',
                    'text'  => $label,
                    'color' => '#888888',
                    'size'  => 'sm',
                    'flex'  => 3,
                ],
                [
                    'type'   => 'text',
                    'text'   => $value,
                    'color'  => '#1a1a2e',
                    'size'   => 'sm',
                    'weight' => 'bold',
                    'flex'   => 5,
                    'wrap'   => true,
                ],
            ],
        ];
    }
}
