<?php
/**
 * Created by PhpStorm.
 * User: Giulio Troccoli <giulio@troccoli.it>
 * Date: 23/08/2016
 * Time: 19:33
 */

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Season;
use Illuminate\Support\Facades\Input;

class LoadController extends Controller
{
    public function uploadFixtures()
    {
        return view('admin.data-management.load.fixtures', ['seasons' => Season::all()]);
    }

    public function startUploadFixtures(Request $request)
    {
        $this->validate($request, [
            'season_id'   => 'required|exists:seasons,id',
            'upload_file' => 'required',
        ]);
    }
}