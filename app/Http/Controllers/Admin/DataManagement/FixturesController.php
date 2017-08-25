<?php

namespace LVA\Http\Controllers\Admin\DataManagement;

use Laracasts\Flash\Flash;
use LVA\Http\Controllers\Controller;
use LVA\Http\Requests\StoreFixtureRequest as StoreRequest;
use LVA\Http\Requests\UpdateFixtureRequest as UpdateRequest;
use LVA\Models\Division;
use LVA\Models\Fixture;
use LVA\Models\Team;
use LVA\Models\Venue;

/**
 * Class FixturesController
 *
 * @package LVA\Http\Controllers\Admin\DataManagement
 */
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
     * @param StoreRequest $request
     *
     * @return mixed
     */
    public function store(StoreRequest $request)
    {
        Fixture::create($request->all());

        Flash::success('Fixture added!');

        return redirect()->route('fixtures.index');
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
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return mixed
     */
    public function update(UpdateRequest $request, $id)
    {
        /** @var Fixture $fixture */
        $fixture = Fixture::findOrFail($id);
        $fixture->update($request->all());

        Flash::success('Fixture updated!');

        return redirect()->route('fixtures.index');
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
        $canBeDeleted = empty(Fixture::find($id)->available_appointments->toArray());
        if ($canBeDeleted) {
            Fixture::destroy($id);
            Flash::success('Fixture deleted!');
        } else {
            Flash::error('Cannot delete because they are existing appointments for this fixture.');
        }

        return redirect()->route('fixtures.index');
    }

}
