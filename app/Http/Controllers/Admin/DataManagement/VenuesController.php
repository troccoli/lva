<?php

namespace LVA\Http\Controllers\Admin\DataManagement;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use LVA\Http\Controllers\Controller;
use LVA\Models\Venue;

/**
 * Class VenuesController.
 */
class VenuesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return mixed
     */
    public function index()
    {
        $venues = Venue::paginate(15);

        return view('admin.data-management.venues.index', compact('venues'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        return view('admin.data-management.venues.create');
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
        $this->validate($request, ['venue' => 'required|unique:venues', 'postcode' => 'uk_postcode']);

        Venue::create($request->all());

        Flash::success('Venue added!');

        return redirect()->route('venues.index');
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
        $venue = Venue::findOrFail($id);

        return view('admin.data-management.venues.show', compact('venue'));
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
        $venue = Venue::findOrFail($id);

        return view('admin.data-management.venues.edit', compact('venue'));
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
        $this->validate($request, ['venue' => 'required|unique:venues,venue,' . $id, 'postcode' => 'uk_postcode']);

        /** @var Venue $venue */
        $venue = Venue::findOrFail($id);
        $venue->update($request->all());

        Flash::success('Venue updated!');

        return redirect()->route('venues.index');
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
        $canBeDeleted = empty(Venue::find($id)->fixtures->toArray());
        if ($canBeDeleted) {
            Venue::destroy($id);
            Flash::success('Venue deleted!');
        } else {
            Flash::error('Cannot delete because they are existing fixtures at this venue.');
        }

        return redirect()->route('venues.index');
    }
}
