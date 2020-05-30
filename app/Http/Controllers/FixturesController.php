<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Policies\CheckRoles;
use Illuminate\View\View;

class FixturesController extends Controller
{
    use CheckRoles;

    public function index(): View
    {
        $this->authorize('viewAny', Fixture::class);

        return view('CRUD.fixtures.index');
    }
}
