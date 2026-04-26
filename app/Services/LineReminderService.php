<?php

namespace App\Services;

use App\Jobs\SendLineQuickReplyJob;
use App\Models\Member;
use App\Repositories\MemberRepository;

class LineReminderService
{
    private const QUICK_REPLY_ITEMS = [
        ['type' => 'action', 'action' => ['type' => 'postback', 'label' => '術前照片',  'data' => 'category=before',   'displayText' => '術前照片']],
        ['type' => 'action', 'action' => ['type' => 'postback', 'label' => '恢復期照片', 'data' => 'category=recovery', 'displayText' => '恢復期照片']],
        ['type' => 'action', 'action' => ['type' => 'postback', 'label' => '術後照片',  'data' => 'category=after',    'displayText' => '術後照片']],
    ];

    public function __construct(
        private readonly MemberRepository $memberRepository,
    ) {}

    public function sendReminderToMember(Member $member): void
    {
        SendLineQuickReplyJob::dispatch(
            $member->line_user_id,
            '提醒您上傳今日追蹤照片，請選擇照片類型：',
            self::QUICK_REPLY_ITEMS
        );
    }

    public function sendRemindersToAllOngoing(): void
    {
        foreach ($this->memberRepository->getMembersWithOngoingFollowUp() as $member) {
            $this->sendReminderToMember($member);
        }
    }
}
