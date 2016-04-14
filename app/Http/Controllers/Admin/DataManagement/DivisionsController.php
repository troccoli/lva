<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Division;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Laracasts\Flash\Flash;

use App\Models\Season;

class DivisionsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $divisions = Division::paginate(15);

        return view('admin.data-management.divisions.index', compact('divisions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.data-management.divisions.create', ['seasons' => Season::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['season_id' => 'required', 'division' => 'required']);

        Division::create($request->all());

        Flash::success('Division added!');

        return redirect('admin/data-management/divisions');
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
        $division = Division::findOrFail($id);

        return view('admin.data-management.divisions.show', compact('division'));
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
        $division = Division::findOrFail($id);
        $seasons = Season::all();

        return view('admin.data-management.divisions.edit', compact('division', 'seasons'));
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
        $this->validate($request, ['division' => 'required', ]);

        $division = Division::findOrFail($id);
        $division->update($request->all());

        Flash::success('Division updated!');

        return redirect('admin/data-management/divisions');
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
        Division::destroy($id);

        Flash::success('Division deleted!');

        return redirect('admin/data-management/divisions');
    }

}
