<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 16:58
 */

namespace App\Http\Controllers\Admin\DataManagement;


use App\Http\Controllers\Controller;

class Seasons extends Controller
{
    public function showHome()
    {
        return view('admin.data-management.seasons.home');
    }
}