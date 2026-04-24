<?php

namespace App\Services;

use App\Jobs\SendLineMessageJob;
use App\Models\Member;
use App\Repositories\MemberRepository;

class LineReminderService
{
    public function __construct(
        private readonly MemberRepository $memberRepository,
    ) {}

    public function sendReminderToMember(Member $member): void
    {
        SendLineMessageJob::dispatch(
            $member->line_user_id,
            "提醒您：請上傳今日的術後追蹤照片，以便醫師了解恢復狀況。感謝您的配合！"
        );
    }

    public function sendRemindersToAllOngoing(): void
    {
        foreach ($this->memberRepository->getMembersWithOngoingFollowUp() as $member) {
            $this->sendReminderToMember($member);
        }
    }
}
