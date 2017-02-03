<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Requests\StoreSeasonRequest as StoreRequest;
use App\Http\Requests\UpdateSeasonRequest as UpdateRequest;
use App\Http\Controllers\Controller;

use Laracasts\Flash\Flash;
use App\Models\Season;

/**
 * Class SeasonsController
 *
 * @package App\Http\Controllers\Admin\DataManagement
 */
class SeasonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        $seasons = Season::paginate(15);

        return view('admin.data-management.seasons.index', compact('seasons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        return view('admin.data-management.seasons.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     *
     * @return mixed
     */
    public function store(StoreRequest $request)
    {
        Season::create($request->all());

        Flash::success('Season added!');

        return redirect('admin/data-management/seasons');
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
        $season = Season::findOrFail($id);

        return view('admin.data-management.seasons.show', compact('season'));
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
        $season = Season::findOrFail($id);

        return view('admin.data-management.seasons.edit', compact('season'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return mixed
     */
    public function update(UpdateRequest $request, $id)
    {
        /** @var Season $season */
        $season = Season::findOrFail($id);
        $season->update($request->all());

        Flash::success('Season updated!');

        return redirect('admin/data-management/seasons');
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
        $canBeDeleted = empty(Season::find($id)->divisions->toArray());
        if ($canBeDeleted) {
            Season::destroy($id);
            Flash::success('Season deleted!');
        } else {
            Flash::error('Cannot delete because they are existing divisions in this season.');
        }

        return redirect('admin/data-management/seasons');

    }

}
