<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Requests\StoreTeamRequest as StoreRequest;
use App\Http\Requests\UpdateTeamRequest as UpdateRequest;
use App\Http\Controllers\Controller;

use Laracasts\Flash\Flash;
use App\Models\Club;
use App\Models\Team;

/**
 * Class TeamsController
 *
 * @package App\Http\Controllers\Admin\DataManagement
 */
class TeamsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        $teams = Team::paginate(15);

        return view('admin.data-management.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        return view('admin.data-management.teams.create', ['clubs' => Club::all()]);
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
        Team::create($request->all());

        Flash::success('Team added!');

        return redirect('admin/data-management/teams');
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
        $team = Team::findOrFail($id);

        return view('admin.data-management.teams.show', compact('team'));
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
        $team = Team::findOrFail($id);
        $clubs = Club::all();

        return view('admin.data-management.teams.edit', compact('team', 'clubs'));
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
        /** @var Team $team */
        $team = Team::findOrFail($id);
        $team->update($request->all());

        Flash::success('Team updated!');

        return redirect('admin/data-management/teams');
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
        $team = Team::find($id);
        $canBeDeleted = empty($team->homeFixtures->toArray()) && empty($team->awayFixtures->toArray());
        if ($canBeDeleted) {
            Team::destroy($id);
            Flash::success('Team deleted!');
        } else {
            Flash::error('Cannot delete because they are existing fixtures for this team.');
        }

        return redirect('admin/data-management/teams');
    }

}
