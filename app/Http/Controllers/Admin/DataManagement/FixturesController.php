<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Division;
use App\Models\Fixture;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Http\Request;

class FixturesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        $fixtures = Fixture::paginate(15);

        return view('admin.data-management.fixtures.index', compact('fixtures'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
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
     * @param Request $request
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->validate($request,
            [
                'division_id'  =>
                    'required|' .
                    'exists:divisions,id|' .
                    'unique:fixtures,division_id,NULL,id' .
                    ',home_team_id,' . $request->get('home_team_id') .
                    ',away_team_id,' . $request->get('away_team_id'),
                'match_number' => 'required|unique:fixtures,match_number,NULL,id,division_id,' . $request->get('division_id'),
                'match_date'   => 'required',
                'warm_up_time' => 'required',
                'start_time'   => 'required',
                'home_team_id' => 'required|exists:teams,id',
                'away_team_id' => 'required|exists:teams,id|different:home_team_id',
                'venue_id'     => 'required|exists:venues,id',
            ],
            [
                'away_team_id.different' => 'The away team cannot be the same as the home team.',
                'division_id.unique'     => 'The fixture for these two teams have already been added in this division.',
                'match_number.unique'    => 'There is already a match with the same number in this division.',
            ]
        );

        Fixture::create($request->all());

        \Flash::success('Fixture added!');

        return redirect('admin/data-management/fixtures');
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
        $fixture = Fixture::findOrFail($id);

        return view('admin.data-management.fixtures.show', compact('fixture'));
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
        $fixture = Fixture::findOrFail($id);
        $divisions = Division::all();
        $teams = Team::all();
        $venues = Venue::all();

        return view('admin.data-management.fixtures.edit', compact('fixture', 'divisions', 'teams', 'venues'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int $id
     *
     * @return mixed
     */
    public function update(Request $request, $id)
    {
        $this->validate($request,
            [
                'division_id'  =>
                    'required|' .
                    'exists:divisions,id|' .
                    'unique:fixtures,division_id,NULL,id' .
                    ',home_team_id,' . $request->get('home_team_id') .
                    ',away_team_id,' . $request->get('away_team_id'),
                'match_number' => 'required|unique:fixtures,match_number,NULL,id,division_id,' . $request->get('division_id'),
                'match_date'   => 'required',
                'warm_up_time' => 'required',
                'start_time'   => 'required',
                'home_team_id' => 'required|exists:teams,id',
                'away_team_id' => 'required|exists:teams,id|different:home_team_id',
                'venue_id'     => 'required|exists:venues,id',
            ],
            [
                'away_team_id.different' => 'The away team cannot be the same as the home team.',
                'division_id.unique'     => 'The fixture for these two teams have already been added in this division.',
                'match_number.unique'    => 'There is already a match with the same number in this division.',
            ]
        );

        $fixture = Fixture::findOrFail($id);
        $fixture->update($request->all());

        \Flash::success('Fixture updated!');

        return redirect('admin/data-management/fixtures');
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
        Fixture::destroy($id);

        \Flash::success('Fixture deleted!');

        return redirect('admin/data-management/fixtures');
    }

}
