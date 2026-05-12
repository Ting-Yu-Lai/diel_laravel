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
            $dayNumber = (int) $followUp->created_at->startOfDay()->diffInDays(now()->startOfDay()) + 1;
            $member    = $followUp->treatmentRecordItem->treatmentRecord->customer->member;

            if ($dayNumber === 7) {
                SendLineFlexMessageJob::dispatch($member->line_user_id, $this->buildAppointmentBubble($followUp, $dayNumber));
            } elseif ($dayNumber % 3 === 0) {
                SendLineFlexMessageJob::dispatch($member->line_user_id, $this->buildStatusCheckBubble($followUp, $dayNumber));
            }
        }
    }

    public function sendReminderForFollowUp(FollowUp $followUp, ?int $forceDay = null, bool $sync = false): void
    {
        $followUp->load([
            'treatmentRecordItem.treatment',
            'treatmentRecordItem.treatmentRecord.customer.member',
        ]);

        $dayNumber = $forceDay ?? (int) $followUp->created_at->startOfDay()->diffInDays(now()->startOfDay()) + 1;
        $member    = $followUp->treatmentRecordItem->treatmentRecord->customer->member;

        $bubble = $dayNumber === 7
            ? $this->buildAppointmentBubble($followUp, $dayNumber)
            : $this->buildStatusCheckBubble($followUp, $dayNumber);

        $sync
            ? SendLineFlexMessageJob::dispatchSync($member->line_user_id, $bubble)
            : SendLineFlexMessageJob::dispatch($member->line_user_id, $bubble);
    }

    private function buildStatusCheckBubble(FollowUp $followUp, int $dayNumber): array
    {
        $item    = $followUp->treatmentRecordItem;
        $record  = $item->treatmentRecord;
        $member  = $record->customer->member;
        $dateStr = $record->record_date->format('Y年m月d日');
        $fid     = $followUp->id;

        $infoRows = [$this->infoRow('療程名稱', $item->treatment->name)];
        if (!empty($item->body_part)) {
            $infoRows[] = $this->infoRow('施作部位', $item->body_part);
        }
        $infoRows[] = $this->infoRow('就診日期', $dateStr);
        $infoRows[] = $this->infoRow('今日天數', '第 ' . $dayNumber . ' 天');

        return [
            'type'   => 'bubble',
            'header' => [
                'type'            => 'box',
                'layout'          => 'vertical',
                'backgroundColor' => '#1a1a2e',
                'paddingAll'      => '20px',
                'contents'        => [
                    ['type' => 'text', 'text' => '新美學診所',   'color' => '#d4a373', 'size' => 'xs',  'weight' => 'bold'],
                    ['type' => 'text', 'text' => '術後狀況關懷', 'color' => '#ffffff', 'size' => 'lg', 'weight' => 'bold', 'margin' => 'sm'],
                ],
            ],
            'body' => [
                'type'     => 'box',
                'layout'   => 'vertical',
                'spacing'  => 'md',
                'contents' => [
                    ['type' => 'text', 'text' => '親愛的 ' . $member->full_name . ' 貴賓，', 'wrap' => true, 'weight' => 'bold', 'size' => 'md', 'color' => '#1a1a2e'],
                    ['type' => 'separator'],
                    ['type' => 'box', 'layout' => 'vertical', 'spacing' => 'sm', 'contents' => $infoRows],
                    ['type' => 'separator'],
                    ['type' => 'text', 'text' => '請問您目前的恢復狀況如何？', 'wrap' => true, 'color' => '#333333', 'size' => 'sm', 'weight' => 'bold'],
                ],
            ],
            'footer' => [
                'type'    => 'box',
                'layout'  => 'vertical',
                'spacing' => 'sm',
                'contents' => [
                    [
                        'type'   => 'button',
                        'style'  => 'primary',
                        'color'  => '#27ae60',
                        'action' => [
                            'type'        => 'postback',
                            'label'       => '✅ 目前沒問題',
                            'data'        => "action=ok&fid={$fid}&day={$dayNumber}",
                            'displayText' => '目前沒問題',
                        ],
                    ],
                    [
                        'type'   => 'button',
                        'style'  => 'primary',
                        'color'  => '#c0392b',
                        'action' => [
                            'type'        => 'postback',
                            'label'       => '⚠️ 有異常，需要通報',
                            'data'        => "action=abnormal&fid={$fid}&day={$dayNumber}",
                            'displayText' => '有異常，需要通報',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function buildAppointmentBubble(FollowUp $followUp, int $dayNumber): array
    {
        $item    = $followUp->treatmentRecordItem;
        $record  = $item->treatmentRecord;
        $member  = $record->customer->member;
        $dateStr = $record->record_date->format('Y年m月d日');

        $infoRows = [$this->infoRow('療程名稱', $item->treatment->name)];
        if (!empty($item->body_part)) {
            $infoRows[] = $this->infoRow('施作部位', $item->body_part);
        }
        $infoRows[] = $this->infoRow('就診日期', $dateStr);
        $infoRows[] = $this->infoRow('今日天數', '第 ' . $dayNumber . ' 天');

        return [
            'type'   => 'bubble',
            'header' => [
                'type'            => 'box',
                'layout'          => 'vertical',
                'backgroundColor' => '#1a1a2e',
                'paddingAll'      => '20px',
                'contents'        => [
                    ['type' => 'text', 'text' => '新美學診所',   'color' => '#d4a373', 'size' => 'xs',  'weight' => 'bold'],
                    ['type' => 'text', 'text' => '回診預約提醒', 'color' => '#ffffff', 'size' => 'lg', 'weight' => 'bold', 'margin' => 'sm'],
                ],
            ],
            'body' => [
                'type'     => 'box',
                'layout'   => 'vertical',
                'spacing'  => 'md',
                'contents' => [
                    ['type' => 'text', 'text' => '親愛的 ' . $member->full_name . ' 貴賓，', 'wrap' => true, 'weight' => 'bold', 'size' => 'md', 'color' => '#1a1a2e'],
                    ['type' => 'separator'],
                    ['type' => 'box', 'layout' => 'vertical', 'spacing' => 'sm', 'contents' => $infoRows],
                    ['type' => 'separator'],
                    [
                        'type'   => 'text',
                        'text'   => '已為您安排第 7 天回診評估，請依約回診讓醫師確認您的恢復狀況。',
                        'wrap'   => true,
                        'color'  => '#1a1a2e',
                        'size'   => 'sm',
                        'weight' => 'bold',
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
                        'text'  => '如無法出席，請致電診所重新預約，我們將為您調整時間。',
                        'color' => '#666666',
                        'size'  => 'sm',
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
                ['type' => 'text', 'text' => $label, 'color' => '#888888', 'size' => 'sm', 'flex' => 3],
                ['type' => 'text', 'text' => $value, 'color' => '#1a1a2e', 'size' => 'sm', 'weight' => 'bold', 'flex' => 5, 'wrap' => true],
            ],
        ];
    }
}
