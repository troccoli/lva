<?php

namespace LVA\Http\Controllers\Admin\DataManagement;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use LVA\Http\Controllers\Controller;
use LVA\Models\Club;
use LVA\Models\Team;

/**
 * Class TeamsController.
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
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'club_id' => 'required|exists:clubs,id',
            'team'    => 'required|unique:teams,team,NULL,id,club_id,'.$request->input('club_id'),
            'trigram' => 'required|alpha_num|size:3|unique:teams,trigram',
        ]);

        Team::create($request->all());

        Flash::success('Team added!');

        return redirect()->route('teams.index');
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
     * @param Request $request
     * @param int     $id
     *
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'club_id' => 'required|exists:clubs,id',
            'team'    => 'required|unique:teams,team,'.$id.',id,club_id,'.$request->input('club_id'),
            'trigram' => 'required|alpha_num|size:3|unique:teams,trigram,'.$id.',id',
        ]);

        /** @var Team $team */
        $team = Team::findOrFail($id);
        $team->update($request->all());

        Flash::success('Team updated!');

        return redirect()->route('teams.index');
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

        return redirect()->route('teams.index');
    }
}
