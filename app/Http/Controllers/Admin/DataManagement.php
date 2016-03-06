<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 11:07
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DataManagement extends Controller
{
    public function showHome()
    {
        return view('admin.data-management.home');
    }
}