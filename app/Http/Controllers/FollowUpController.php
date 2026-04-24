<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\StoreFollowUpLogRequest;
use App\Http\Requests\Admin\StoreFollowUpPhotoRequest;
use App\Http\Requests\Admin\UpdateFollowUpLogRequest;
use App\Models\FollowUpLog;
use App\Models\FollowUpPhoto;
use App\Models\TreatmentRecordItem;
use App\Services\FollowUpLogService;
use App\Services\FollowUpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FollowUpController extends Controller
{
    public function __construct(
        private readonly FollowUpService    $followUpService,
        private readonly FollowUpLogService $logService,
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
            'treatmentRecordItem.treatmentRecord.customer.member'
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

    public function storeLog(StoreFollowUpLogRequest $request, int $followUpId)
    {
        $this->logService->create($followUpId, $request->only(['day_number', 'content']));

        return redirect()->route('backend.follow-up.show', $followUpId)
            ->with('success', '追蹤紀錄已新增');
    }

    public function editLog(int $followUpId, int $logId)
    {
        $log     = $this->logService->find($logId);
        $followUp = $this->followUpService->findWithLogs($followUpId);
        $followUp->load(
            'treatmentRecordItem.treatment',
            'treatmentRecordItem.treatmentRecord.customer'
        );

        return view('backend.follow-up.log-edit', compact('followUp', 'log'));
    }

    public function updateLog(UpdateFollowUpLogRequest $request, int $followUpId, int $logId)
    {
        $this->logService->update($logId, $request->only(['day_number', 'content']));

        return redirect()->route('backend.follow-up.show', $followUpId)
            ->with('success', '追蹤紀錄已更新');
    }

    public function destroyLog(Request $request, int $followUpId, int $logId)
    {
        if (Session::get('power') != 1) {
            return back()->with('error', '你沒有權限執行此操作');
        }

        $request->validate([
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => '請填寫刪除原因',
        ]);

        $log = $this->logService->findWithPhotos($logId);

        foreach ($log->photos as $photo) {
            if (file_exists(public_path($photo->photo_url))) {
                unlink(public_path($photo->photo_url));
            }
        }

        $this->logService->delete($logId, $request->reason, (int) Session::get('admin_id'));

        return redirect()->route('backend.follow-up.show', $followUpId)
            ->with('success', '追蹤紀錄已刪除');
    }

    public function storePhoto(StoreFollowUpPhotoRequest $request, int $logId)
    {
        $dir = public_path('uploads/follow-up');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        foreach ($request->file('photos') as $file) {
            $ext      = $file->getClientOriginalExtension();
            $fileName = uniqid('followup_') . '.' . $ext;
            $file->move($dir, $fileName);

            FollowUpPhoto::create([
                'follow_up_log_id' => $logId,
                'photo_url'        => '/uploads/follow-up/' . $fileName,
                'category'         => $request->category,
            ]);
        }

        $log   = FollowUpLog::findOrFail($logId);
        $count = count($request->file('photos'));

        return redirect()->route('backend.follow-up.show', $log->follow_up_id)
            ->with('success', "已上傳 {$count} 張照片");
    }

    public function destroyPhoto(int $logId, int $photoId)
    {
        $photo = FollowUpPhoto::findOrFail($photoId);

        if (file_exists(public_path($photo->photo_url))) {
            unlink(public_path($photo->photo_url));
        }

        $log = FollowUpLog::findOrFail($logId);
        $photo->delete();

        return redirect()->route('backend.follow-up.show', $log->follow_up_id)
            ->with('success', '照片已刪除');
    }
}
