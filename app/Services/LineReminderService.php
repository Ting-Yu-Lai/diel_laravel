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

        $infoItems = [
            ['icon' => '✦', 'label' => '療程名稱', 'value' => $item->treatment->name],
            ['icon' => '◎', 'label' => '就診日期', 'value' => $dateStr],
        ];
        if (!empty($item->body_part)) {
            $infoItems[] = ['icon' => '▸', 'label' => '施作部位', 'value' => $item->body_part];
        }

        return [
            'type'   => 'bubble',
            'header' => [
                'type'            => 'box',
                'layout'          => 'vertical',
                'backgroundColor' => '#2D1B69',
                'paddingAll'      => '20px',
                'contents'        => [
                    [
                        'type'     => 'box',
                        'layout'   => 'horizontal',
                        'contents' => [
                            [
                                'type'   => 'text',
                                'text'   => '新美學診所',
                                'color'  => '#C9956A',
                                'size'   => 'xs',
                                'weight' => 'bold',
                                'flex'   => 1,
                            ],
                            [
                                'type'            => 'box',
                                'layout'          => 'vertical',
                                'backgroundColor' => '#C9956A',
                                'cornerRadius'    => '20px',
                                'paddingStart'    => '10px',
                                'paddingEnd'      => '10px',
                                'paddingTop'      => '4px',
                                'paddingBottom'   => '4px',
                                'contents'        => [
                                    [
                                        'type'   => 'text',
                                        'text'   => "第 {$dayNumber} 天",
                                        'color'  => '#FFFFFF',
                                        'size'   => 'xs',
                                        'weight' => 'bold',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'   => 'text',
                        'text'   => '術後照護關懷',
                        'color'  => '#FFFFFF',
                        'size'   => 'xl',
                        'weight' => 'bold',
                        'margin' => 'md',
                    ],
                    [
                        'type'   => 'text',
                        'text'   => '我們關心您每一天的恢復狀況',
                        'color'  => '#B8A0D8',
                        'size'   => 'xs',
                        'margin' => 'xs',
                    ],
                ],
            ],
            'body' => [
                'type'            => 'box',
                'layout'          => 'vertical',
                'spacing'         => 'lg',
                'paddingAll'      => '20px',
                'backgroundColor' => '#FAFAFA',
                'contents'        => [
                    [
                        'type'   => 'text',
                        'text'   => $member->full_name . ' 貴賓您好 👋',
                        'weight' => 'bold',
                        'size'   => 'md',
                        'color'  => '#2D1B69',
                    ],
                    [
                        'type'            => 'box',
                        'layout'          => 'vertical',
                        'backgroundColor' => '#FFFFFF',
                        'cornerRadius'    => '12px',
                        'paddingAll'      => '16px',
                        'spacing'         => 'md',
                        'contents'        => $this->buildInfoRows($infoItems),
                    ],
                    [
                        'type'            => 'box',
                        'layout'          => 'vertical',
                        'backgroundColor' => '#F0EBFF',
                        'cornerRadius'    => '10px',
                        'paddingAll'      => '14px',
                        'contents'        => [
                            [
                                'type'   => 'text',
                                'text'   => '🌸 今日恢復狀況如何？',
                                'weight' => 'bold',
                                'size'   => 'sm',
                                'color'  => '#2D1B69',
                            ],
                            [
                                'type'   => 'text',
                                'text'   => '請選擇您目前的狀態，讓我們即時掌握您的恢復進度',
                                'size'   => 'xs',
                                'color'  => '#7B6FA0',
                                'wrap'   => true,
                                'margin' => 'sm',
                            ],
                        ],
                    ],
                ],
            ],
            'footer' => [
                'type'            => 'box',
                'layout'          => 'vertical',
                'spacing'         => 'sm',
                'paddingAll'      => '16px',
                'backgroundColor' => '#FFFFFF',
                'contents'        => [
                    [
                        'type'   => 'button',
                        'style'  => 'primary',
                        'color'  => '#27AE60',
                        'height' => 'sm',
                        'action' => [
                            'type'        => 'postback',
                            'label'       => '✅  一切順利，狀況良好',
                            'data'        => "action=ok&fid={$fid}&day={$dayNumber}",
                            'displayText' => '一切順利，狀況良好',
                        ],
                    ],
                    [
                        'type'   => 'button',
                        'style'  => 'primary',
                        'color'  => '#E74C3C',
                        'height' => 'sm',
                        'action' => [
                            'type'        => 'postback',
                            'label'       => '⚠️  有異常，需要協助',
                            'data'        => "action=abnormal&fid={$fid}&day={$dayNumber}",
                            'displayText' => '有異常，需要協助',
                        ],
                    ],
                    [
                        'type'   => 'text',
                        'text'   => '如有緊急狀況請直接致電診所',
                        'size'   => 'xs',
                        'color'  => '#BBBBBB',
                        'align'  => 'center',
                        'margin' => 'sm',
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

        $infoItems = [
            ['icon' => '✦', 'label' => '療程名稱', 'value' => $item->treatment->name],
            ['icon' => '◎', 'label' => '就診日期', 'value' => $dateStr],
        ];
        if (!empty($item->body_part)) {
            $infoItems[] = ['icon' => '▸', 'label' => '施作部位', 'value' => $item->body_part];
        }

        return [
            'type'   => 'bubble',
            'header' => [
                'type'            => 'box',
                'layout'          => 'vertical',
                'backgroundColor' => '#0D4F6B',
                'paddingAll'      => '20px',
                'contents'        => [
                    [
                        'type'     => 'box',
                        'layout'   => 'horizontal',
                        'contents' => [
                            [
                                'type'   => 'text',
                                'text'   => '新美學診所',
                                'color'  => '#7EC8E3',
                                'size'   => 'xs',
                                'weight' => 'bold',
                                'flex'   => 1,
                            ],
                            [
                                'type'            => 'box',
                                'layout'          => 'vertical',
                                'backgroundColor' => '#7EC8E3',
                                'cornerRadius'    => '20px',
                                'paddingStart'    => '10px',
                                'paddingEnd'      => '10px',
                                'paddingTop'      => '4px',
                                'paddingBottom'   => '4px',
                                'contents'        => [
                                    [
                                        'type'   => 'text',
                                        'text'   => '第 7 天回診',
                                        'color'  => '#0D4F6B',
                                        'size'   => 'xs',
                                        'weight' => 'bold',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'type'   => 'text',
                        'text'   => '📅  回診預約提醒',
                        'color'  => '#FFFFFF',
                        'size'   => 'xl',
                        'weight' => 'bold',
                        'margin' => 'md',
                    ],
                    [
                        'type'   => 'text',
                        'text'   => '您的 7 天術後追蹤期即將告一段落',
                        'color'  => '#9DD7EC',
                        'size'   => 'xs',
                        'margin' => 'xs',
                    ],
                ],
            ],
            'body' => [
                'type'            => 'box',
                'layout'          => 'vertical',
                'spacing'         => 'lg',
                'paddingAll'      => '20px',
                'backgroundColor' => '#FAFAFA',
                'contents'        => [
                    [
                        'type'   => 'text',
                        'text'   => $member->full_name . ' 貴賓您好 🌟',
                        'weight' => 'bold',
                        'size'   => 'md',
                        'color'  => '#0D4F6B',
                    ],
                    [
                        'type'            => 'box',
                        'layout'          => 'vertical',
                        'backgroundColor' => '#FFFFFF',
                        'cornerRadius'    => '12px',
                        'paddingAll'      => '16px',
                        'spacing'         => 'md',
                        'contents'        => $this->buildInfoRows($infoItems),
                    ],
                    [
                        'type'            => 'box',
                        'layout'          => 'vertical',
                        'backgroundColor' => '#E6F4FA',
                        'cornerRadius'    => '10px',
                        'paddingAll'      => '14px',
                        'contents'        => [
                            [
                                'type'   => 'text',
                                'text'   => '📋 回診評估說明',
                                'weight' => 'bold',
                                'size'   => 'sm',
                                'color'  => '#0D4F6B',
                            ],
                            [
                                'type'   => 'text',
                                'text'   => '已為您安排第 7 天回診評估，請依約回診讓醫師確認您的恢復狀況，確保療效達到最佳效果。',
                                'size'   => 'xs',
                                'color'  => '#4A7D90',
                                'wrap'   => true,
                                'margin' => 'sm',
                            ],
                        ],
                    ],
                ],
            ],
            'footer' => [
                'type'            => 'box',
                'layout'          => 'vertical',
                'spacing'         => 'sm',
                'paddingAll'      => '16px',
                'backgroundColor' => '#FFFFFF',
                'contents'        => [
                    [
                        'type'  => 'text',
                        'text'  => '如需調整回診時間，請致電診所，我們將為您重新安排。',
                        'size'  => 'sm',
                        'color' => '#555555',
                        'align' => 'center',
                        'wrap'  => true,
                    ],
                    [
                        'type'   => 'text',
                        'text'   => '期待在診所再次為您服務 ✨',
                        'size'   => 'xs',
                        'color'  => '#BBBBBB',
                        'align'  => 'center',
                        'margin' => 'sm',
                    ],
                ],
            ],
        ];
    }

    private function buildInfoRows(array $items): array
    {
        $rows = [];
        foreach ($items as $i => $item) {
            if ($i > 0) {
                $rows[] = ['type' => 'separator', 'color' => '#F0F0F0'];
            }
            $rows[] = [
                'type'     => 'box',
                'layout'   => 'horizontal',
                'contents' => [
                    [
                        'type'  => 'text',
                        'text'  => $item['icon'],
                        'color' => '#C9956A',
                        'size'  => 'sm',
                        'flex'  => 1,
                    ],
                    [
                        'type'  => 'text',
                        'text'  => $item['label'],
                        'color' => '#999999',
                        'size'  => 'sm',
                        'flex'  => 3,
                    ],
                    [
                        'type'   => 'text',
                        'text'   => $item['value'],
                        'color'  => '#222222',
                        'size'   => 'sm',
                        'weight' => 'bold',
                        'flex'   => 5,
                        'wrap'   => true,
                    ],
                ],
            ];
        }
        return $rows;
    }
}
