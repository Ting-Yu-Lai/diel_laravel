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
        if($direction === 'up') {
            //從 carousels 資料表裡，找出 order_num 比目前這筆更小的所有資料。
            $target = Carousel::where('order_num', '<', $carousel->order_num)
            // 將結果按照 order_num 由大排到小。
                ->orderBy('order_num','desc')
            // 只取出結果的「第一筆」。
                ->first();
        } else {
            //從 carousels 資料表裡，找出 order_num 比目前這筆更小的所有資料。
            $target = Carousel::where('order_num', '<', $carousel->order_num)
                ->orderBy('order_num','asc')
                ->first();
        }

        if(!$target) {
            return redirect()->back()->with('message', '已經到最' . ($direction === 'up' ? '上':'下'));
        }

        $temp = $carousel->order_num;
        $carousel->order_num = $target->order_num;
        $target->oder_num = $temp;
        
        $carousel->save();
        $target->save();

        return redirect()->back()->with('message', '排序已更新!');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carousel $carousel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carousel $carousel)
    {
        //
    }
}
