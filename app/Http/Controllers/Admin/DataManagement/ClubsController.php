<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Requests\StoreClubRequest as StoreRequest;
use App\Http\Requests\UpdateClubRequest as UpdateRequest;
use App\Http\Controllers\Controller;

use Laracasts\Flash\Flash;
use App\Models\Club;

/**
 * Class ClubsController
 *
 * @package App\Http\Controllers\Admin\DataManagement
 */
class ClubsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        $clubs = Club::paginate(15);

        return view('admin.data-management.clubs.index', compact('clubs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        return view('admin.data-management.clubs.create');
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
        Club::create($request->all());

        Flash::success('Club added!');

        return redirect('admin/data-management/clubs');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return mixed
     */
    public function show($id)
    {
        $club = Club::findOrFail($id);

        return view('admin.data-management.clubs.show', compact('club'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return mixed
     */
    public function edit($id)
    {
        $club = Club::findOrFail($id);

        return view('admin.data-management.clubs.edit', compact('club'));
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
        /** @var Club $club */
        $club = Club::findOrFail($id);
        $club->update($request->all());

        Flash::success('Club updated!');

        return redirect('admin/data-management/clubs');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        $canBeDeleted = empty(Club::find($id)->teams->toArray());
        if ($canBeDeleted) {
            Club::destroy($id);
            Flash::success('Club deleted!');
        } else {
            Flash::error('Cannot delete because they are existing teams in this club.');
        }

        return redirect('admin/data-management/clubs');
    }

}
