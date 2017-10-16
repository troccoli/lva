<?php

namespace LVA\Http\Controllers;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function showHome()
    {
        return view('home');
    }
}
