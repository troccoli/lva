<?php
/**
 * Created by PhpStorm.
 * User: giulio
 * Date: 06/03/2016
 * Time: 11:07.
 */

namespace LVA\Http\Controllers\Admin;

use LVA\Http\Controllers\Controller;

class DataManagementController extends Controller
{
    public function showHome()
    {
        return view('admin.data-management.home');
    }
}
