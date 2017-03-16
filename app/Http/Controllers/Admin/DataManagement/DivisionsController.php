<?php

namespace LVA\Http\Controllers\Admin\DataManagement;

use Illuminate\Http\Request;
use LVA\Http\Controllers\Controller;

use Laracasts\Flash\Flash;
use LVA\Models\Division;
use LVA\Models\Season;

/**
 * Class DivisionsController
 *
 * @package LVA\Http\Controllers\Admin\DataManagement
 */
class DivisionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        $divisions = Division::paginate(15);

        return view('admin.data-management.divisions.index', compact('divisions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        return view('admin.data-management.divisions.create', ['seasons' => Season::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'season_id' => 'required|exists:seasons,id',
            'division'  => 'required|unique:divisions,division,NULL,id,season_id,' . $request->input('season_id'),
        ]);

        Division::create($request->all());

        Flash::success('Division added!');

        return redirect('admin/data-management/divisions');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $division = Division::findOrFail($id);

        return view('admin.data-management.divisions.show', compact('division'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return mixed
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
     * @param Request $request
     * @param int     $id
     *
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'season_id' => 'required|exists:seasons,id',
            'division'  => 'required|unique:divisions,division,' . $id . ',id,season_id,' . $request->input('season_id'),
        ]);

        /** @var Division $division */
        $division = Division::findOrFail($id);
        $division->update($request->all());

        Flash::success('Division updated!');

        return redirect('admin/data-management/divisions');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $canBeDeleted = empty(Division::find($id)->fixtures->toArray());
        if ($canBeDeleted) {
            Division::destroy($id);
            Flash::success('Division deleted!');
        } else {
            Flash::error('Cannot delete because they are existing fixtures in this division.');
        }

        return redirect('admin/data-management/divisions');
    }

}
