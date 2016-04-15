<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Division;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use Laracasts\Flash\Flash;

class FixturesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $fixtures = Fixture::paginate(15);

        return view('admin.data-management.fixtures.index', compact('fixtures'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $divisions = Division::all();
        $teams = Team::all();
        $venues = Venue::all();

        return view('admin.data-management.fixtures.create', compact('divisions', 'teams', 'venues'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'division_id'  => 'required',
            'match_number' => 'required',
            'match_date'   => 'required',
            'warm_up_time' => 'required',
            'start_time'   => 'required',
            'home_team_id' => 'required',
            'away_team_id' => 'required',
            'venue_id'     => 'required',
        ]);

        Fixture::create($request->all());

        Flash::success('Fixture added!');

        return redirect('admin/data-management/fixtures');
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
        $fixture = Fixture::findOrFail($id);

        return view('admin.data-management.fixtures.show', compact('fixture'));
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
        $fixture = Fixture::findOrFail($id);
        $divisions = Division::all();
        $teams = Team::all();
        $venues = Venue::all();
        
        return view('admin.data-management.fixtures.edit', compact('fixture', 'divisions', 'teams', 'venues'));
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
        $this->validate($request, [
            'division_id'  => 'required',
            'match_number' => 'required',
            'match_date'   => 'required',
            'warm_up_time' => 'required',
            'start_time'   => 'required',
            'home_team_id' => 'required',
            'away_team_id' => 'required',
            'venue_id'     => 'required',
        ]);
        
        $fixture = Fixture::findOrFail($id);
        $fixture->update($request->all());

        Flash::success('Fixture updated!');

        return redirect('admin/data-management/fixtures');
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
        Fixture::destroy($id);

        Flash::success('Fixture deleted!');

        return redirect('admin/data-management/fixtures');
    }

}
