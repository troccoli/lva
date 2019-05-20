<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): Renderable
    {
        return view('dashboard');
    }
}
