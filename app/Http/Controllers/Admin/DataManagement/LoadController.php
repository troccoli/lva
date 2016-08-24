<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli <giulio@troccoli.it>
 * Date: 23/08/2016
 * Time: 19:33
 */

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;

use App\Models\Season;

class LoadController extends Controller
{
    public function loadFixtures()
    {
        return view('admin.data-management.load.fixtures', ['seasons' => Season::all()]);
    }

    public function loadFixturesGo()
    {

    }
}