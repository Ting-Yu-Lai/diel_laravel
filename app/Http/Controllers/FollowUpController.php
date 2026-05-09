<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreFollowUpPhotoRequest;
use App\Models\FollowUp;
use App\Models\FollowUpPhoto;
use App\Models\TreatmentRecordItem;
use App\Repositories\FollowUpLogRepository;
use App\Services\FollowUpService;
use App\Services\LineReminderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function __construct(
        private readonly FollowUpService       $followUpService,
        private readonly FollowUpLogRepository $logRepo,
        private readonly LineReminderService   $lineReminderService,
    ) {}

    public function showForItem(int $itemId)
    {
        $item     = TreatmentRecordItem::findOrFail($itemId);
        $followUp = $item->followUp ?? $this->followUpService->createForItem($item->id);

        return redirect()->route('backend.follow-up.show', $followUp->id);
    }

    public function show(int $id)
    {
        $followUp = $this->followUpService->findWithLogs($id);
        $followUp->load(
            'treatmentRecordItem.treatment',
            'treatmentRecordItem.treatmentRecord.customer.member',
            'preOpPhotos',
            'postOpPhotos',
        );

        return view('backend.follow-up.show', compact('followUp'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:ongoing,completed,abnormal',
            'notes'  => 'nullable|string|max:5000',
        ], [
            'status.required' => '請選擇追蹤狀態',
            'status.in'       => '追蹤狀態無效',
        ]);

        $this->followUpService->update($id, $request->only(['status', 'notes']));

        return redirect()->route('backend.follow-up.show', $id)
            ->with('success', '追蹤狀態已更新');
    }

    public function storePhoto(StoreFollowUpPhotoRequest $request, int $followUpId): RedirectResponse
    {
        $followUp = FollowUp::findOrFail($followUpId);
        $category = $request->category;

        $dir = public_path('uploads/follow-up');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $logId = null;
        if ($category === 'recovery') {
            $dayNumber = (int) $followUp->created_at->startOfDay()->diffInDays(now()->startOfDay()) + 1;
            $log = $this->logRepo->findByFollowUpAndDay($followUpId, $dayNumber)
                ?? $this->logRepo->create([
                    'follow_up_id' => $followUpId,
                    'day_number'   => $dayNumber,
                    'content'      => null,
                ]);
            $logId = $log->id;
        }

        foreach ($request->file('photos') as $file) {
            $ext      = $file->getClientOriginalExtension();
            $fileName = uniqid('followup_') . '.' . $ext;
            $file->move($dir, $fileName);

            FollowUpPhoto::create([
                'follow_up_id'     => $category !== 'recovery' ? $followUpId : null,
                'follow_up_log_id' => $category === 'recovery' ? $logId : null,
                'photo_url'        => '/uploads/follow-up/' . $fileName,
                'category'         => $category,
            ]);
        }

        if ($category === 'after') {
            $this->followUpService->update($followUpId, ['status' => 'completed']);
        }

        $count = count($request->file('photos'));

        return redirect()->route('backend.follow-up.show', $followUpId)
            ->with('success', "已上傳 {$count} 張照片");
    }

    public function sendReminder(int $id): RedirectResponse
    {
        $followUp = FollowUp::findOrFail($id);

        if ($followUp->status !== 'ongoing') {
            return redirect()->route('backend.follow-up.show', $id)
                ->with('error', '此追蹤已非進行中，無法發送提醒。');
        }

        $member = $followUp->load('treatmentRecordItem.treatmentRecord.customer.member')
            ->treatmentRecordItem->treatmentRecord->customer->member ?? null;

        if (!$member?->line_user_id) {
            return redirect()->route('backend.follow-up.show', $id)
                ->with('error', '此客戶尚未綁定 LINE，無法發送提醒。');
        }

        $this->lineReminderService->sendReminderForFollowUp($followUp);

        return redirect()->route('backend.follow-up.show', $id)
            ->with('success', 'LINE 提醒已排入佇列，即將發送。');
    }

    public function destroyPhoto(int $photoId): RedirectResponse
    {
        $photo = FollowUpPhoto::with('followUpLog')->findOrFail($photoId);

        if (file_exists(public_path($photo->photo_url))) {
            unlink(public_path($photo->photo_url));
        }

        $followUpId = $photo->follow_up_id ?? $photo->followUpLog?->follow_up_id;

        $photo->delete();

        return redirect()->route('backend.follow-up.show', $followUpId)
            ->with('success', '照片已刪除');
    }
}
