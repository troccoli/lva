<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Club;
use App\Models\Team;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Laracasts\Flash\Flash;

class TeamsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $teams = Team::paginate(15);

        return view('admin.data-management.teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.data-management.teams.create', ['clubs' => Club::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['club_id' => 'required', 'team' => 'required',]);

        Team::create($request->all());

        Flash::success('Team added!');

        return redirect('admin/data-management/teams');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $team = Team::findOrFail($id);

        return view('admin.data-management.teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return Response
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
     * @param  int $id
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        $this->validate($request, ['club_id' => 'required', 'team' => 'required']);

        $team = Team::findOrFail($id);
        $team->update($request->all());

        Flash::success('Team updated!');

        return redirect('admin/data-management/teams');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $canBeDeleted = empty(Team::find($id)->fixtures->toArray());
        if ($canBeDeleted) {
            Team::destroy($id);
            Flash::success('Team deleted!');
        } else {
            Flash::error('Cannot delete because they are existing fixtures for this team.');
        }

        return redirect('admin/data-management/teams');
    }

}
