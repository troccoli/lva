<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FixturesController extends Controller
{
    public function index(): View
    {
        return view('CRUD.fixtures.index');
    }
}
