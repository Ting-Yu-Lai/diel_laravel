<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontController extends Controller
{
    //

    public function index() 
    {
        // dd("hello");
        return view('front.index');
    }
}
