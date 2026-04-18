<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Session;

class BackController extends Controller
{
    public function index()
    {
        // 進入後台驗證是否存在session
        if(!Session::has('admin_id')) {
            return redirect()->route('admin.loginForm');
        }
        
        return view('backend.index');
    }
}
