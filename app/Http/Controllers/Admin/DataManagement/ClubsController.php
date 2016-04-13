<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Club;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Laracasts\Flash\Flash;

class ClubsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $clubs = Club::paginate(15);

        return view('admin.data-management.clubs.index', compact('clubs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.data-management.clubs.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        
        Club::create($request->all());

        Flash::success('Club added!');

        return redirect('admin/data-management/clubs');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function show($id)
    {
        $club = Club::findOrFail($id);

        return view('admin.data-management.clubs.show', compact('club'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $club = Club::findOrFail($id);

        return view('admin.data-management.clubs.edit', compact('club'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        
        $club = Club::findOrFail($id);
        $club->update($request->all());

        Flash::success('Club updated!');

        return redirect('admin/data-management/clubs');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        Club::destroy($id);

        Flash::success('Club deleted!');

        return redirect('admin/data-management/clubs');
    }

}
