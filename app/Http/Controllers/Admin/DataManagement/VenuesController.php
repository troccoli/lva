<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Venue;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;

class VenuesController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $venues = Venue::paginate(15);

        return view('admin.data-management.venues.index', compact('venues'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.data-management.venues.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        
        Venue::create($request->all());

        Flass::success('Venue added!');

        return redirect('admin/data-management/venues');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function show($id)
    {
        $venue = Venue::findOrFail($id);

        return view('admin.data-management.venues.show', compact('venue'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $venue = Venue::findOrFail($id);

        return view('admin.data-management.venues.edit', compact('venue'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        
        $venue = Venue::findOrFail($id);
        $venue->update($request->all());

        Flash::success('Venue updated!');

        return redirect('admin/data-management/venues');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        Venue::destroy($id);

        Flash::success('Venue deleted!');

        return redirect('admin/data-management/venues');
    }

}
