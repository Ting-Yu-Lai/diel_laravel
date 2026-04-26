<?php

namespace App\Services;

use App\Repositories\FollowUpLogRepository;
use App\Repositories\FollowUpPhotoRepository;
use App\Repositories\FollowUpRepository;
use App\Repositories\LinePhotoPendingRepository;
use App\Repositories\MemberRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LinePhotoService
{
    public function __construct(
        private readonly MemberRepository             $memberRepository,
        private readonly FollowUpRepository           $followUpRepository,
        private readonly FollowUpLogRepository        $followUpLogRepository,
        private readonly FollowUpPhotoRepository      $followUpPhotoRepository,
        private readonly LineService                  $lineService,
        private readonly LinePhotoPendingRepository   $pendingRepository,
    ) {}

    public function handleIncomingPhoto(string $lineUserId, string $messageId): ?string
    {
        Log::info('[LinePhoto] start', ['lineUserId' => $lineUserId, 'messageId' => $messageId]);

        $member = $this->memberRepository->findByLineUserId($lineUserId);
        if (! $member) {
            Log::warning('[LinePhoto] member not found', ['lineUserId' => $lineUserId]);
            return null;
        }
        Log::info('[LinePhoto] member found', ['memberId' => $member->id]);

        $followUp = $this->followUpRepository->findLatestOngoingByMemberId($member->id);
        if (! $followUp) {
            Log::warning('[LinePhoto] no ongoing follow-up', ['memberId' => $member->id]);
            return '目前沒有進行中的追蹤項目，無法儲存照片。';
        }
        Log::info('[LinePhoto] followUp found', ['followUpId' => $followUp->id]);

        $recordDate = $followUp->treatmentRecordItem->treatmentRecord->record_date;
        $dayNumber  = (int) Carbon::parse($recordDate)->diffInDays(Carbon::today());

        // 主動選擇優先；fallback 依同日有無 log 判斷
        $category = $this->pendingRepository->popCategory($lineUserId);
        if (! $category) {
            $existingLog = $this->followUpLogRepository->findByFollowUpAndDay($followUp->id, $dayNumber);
            $category    = $existingLog ? 'recovery' : 'before';
        }
        Log::info('[LinePhoto] day/category', ['dayNumber' => $dayNumber, 'category' => $category]);

        $log = $this->followUpLogRepository->findByFollowUpAndDay($followUp->id, $dayNumber)
            ?? $this->followUpLogRepository->create([
                'follow_up_id' => $followUp->id,
                'day_number'   => $dayNumber,
                'content'      => null,
            ]);
        Log::info('[LinePhoto] log ready', ['logId' => $log->id]);

        Log::info('[LinePhoto] downloading from LINE...');
        $binary = $this->lineService->downloadMessageContent($messageId);
        Log::info('[LinePhoto] download done', ['bytes' => strlen($binary)]);

        $dir = public_path('uploads/follow-up');
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $fileName = uniqid('followup_') . '.jpg';
        $written  = file_put_contents("{$dir}/{$fileName}", $binary);
        Log::info('[LinePhoto] file written', ['path' => "{$dir}/{$fileName}", 'written' => $written]);

        $this->followUpPhotoRepository->create([
            'follow_up_log_id' => $log->id,
            'photo_url'        => '/uploads/follow-up/' . $fileName,
            'category'         => $category,
        ]);
        Log::info('[LinePhoto] photo record created', ['fileName' => $fileName]);

        $labels = ['before' => '術前', 'recovery' => '恢復期', 'after' => '術後'];
        $label  = $labels[$category] ?? $category;
        return "照片已收到並儲存（第 {$dayNumber} 天 / {$label}照片）。感謝您的回傳！";
    }
}
