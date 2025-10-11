<?php

namespace App\Http\Controllers;

use App\Models\Carousel;
use Illuminate\Http\Request;

class CarouselController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $carousels = Carousel::orderBy('order_num')->get();
        return view('admin.carousel.index', compact('carousels'));
    }

    /**
     * Swap order_num.
     * id: 傳入現有id
     * direction: 移動的位置(往上、往下)
     */
    public function swapOrder($id, $direction)
    {
        $carousel = Carousel::findOrfail($id);
        // === 的優點嚴格比較: === 運算符會同時比較值的型別和值本身，如果兩者型別不同，它會直接回傳 false。 
        if ($direction === 'up') {
            //從 carousels 資料表裡，找出 order_num 比目前這筆更小的所有資料。
            $target = Carousel::where('order_num', '<', $carousel->order_num)
                // 將結果按照 order_num 由大排到小。
                ->orderBy('order_num', 'desc')
                // 只取出結果的「第一筆」。
                ->first();
        } else {
            //從 carousels 資料表裡，找出 order_num 比目前這筆更小的所有資料。
            $target = Carousel::where('order_num', '>', $carousel->order_num)
                ->orderBy('order_num', 'asc')
                ->first();
        }

        if (!$target) {
            return redirect()->back()->with('message', '已經到最' . ($direction === 'up' ? '上' : '下'));
        }

        $temp = $carousel->order_num;
        $carousel->order_num = $target->order_num;
        $target->order_num = $temp;

        $carousel->save();
        $target->save();

        return redirect()->back()->with('message', '排序已更新!');
    }

    /**
     * 修改輪播圖顯示狀態.
     */
    public function toggleActive($id)
    {
        //
        $carousel = Carousel::findOrfail($id);
        $carousel->is_active = !$carousel->is_active;
        $carousel->save();

        return redirect()->back()->with('success', '狀態已更新！');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('admin.carousel.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd('進到 store 了');
        // $validated = $request->all();
        $validated = $request->validate([
            'title' => 'nullable|string|max:100',
            'image' => 'required|image|max:2048',
            'link' => 'nullable|string|max:255',
            'is_active' => 'sometimes|accepted',
        ]);
        // dd(session('errors'));

        // 取得當前最大 order_num
        $newOrder = Carousel::max('order_num') + 1;
        // dd($newOrder);
        // 處理圖片檔案
        $ext = $request->file('image')->getClientOriginalExtension();
        $fileName = uniqid('carousel_') . "." . $ext;

        // 移動檔案到 public/uploads/carousel
        $request->file('image')->move(public_path('uploads/carousel'), $fileName);

        Carousel::create([
            'title' => $validated['title'] ?? null,
            'image_url' => '/uploads/carousel/' . $fileName,
            'link' => $validated['link'] ?? null,
            'order_num' => $newOrder,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('admin.carousel.index')->with('success', '新增成功！');
    }

    /**
     * Display the specified resource.
     */
    public function show(Carousel $carousel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Carousel $carousel)
    {
        $carousel = Carousel::findOrFail($carousel->id);
        return view('admin.carousel.edit', compact('carousel'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        // dd($request->all());
        $carousel = Carousel::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string|max:100',
            'image' => 'nullable|image|max:2048', // 編輯時可以不換圖片
            'link'  => 'nullable|string|max:255',
            'is_active' => 'sometimes|accepted',
        ]);

        // 如果有新圖片，上傳並覆蓋原本
        if ($request->hasFile('image')) {
            $ext = $request->file('image')->getClientOriginalExtension();
            $fileName = uniqid('carousel_') . '.' . $ext;
            $request->file('image')->move(public_path('uploads/carousel'), $fileName);
            // 刪除舊圖片
            if ($carousel->image_url && file_exists(public_path($carousel->image_url))) {
                unlink(public_path($carousel->image_url));
            }
            $carousel->image_url = '/uploads/carousel/' . $fileName;
        }

        // 更新其他欄位
        $carousel->title = $validated['title'] ?? $carousel->title;
        $carousel->link  = $validated['link'] ?? $carousel->link;
        $carousel->is_active = $request->has('is_active') ? 1 : 0;
        $carousel->save();

        return redirect()->route('admin.carousel.index')->with('success', '更新成功！');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carousel $carousel)
    {
        //
    }
}
