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
            "提醒您上傳今日恢復期追蹤照片 📷\n請直接在此傳送照片即可，系統會自動儲存。"
        );
    }

    public function sendRemindersToAllOngoing(): void
    {
        foreach ($this->memberRepository->getMembersWithOngoingFollowUp() as $member) {
            $this->sendReminderToMember($member);
        }
    }
}
