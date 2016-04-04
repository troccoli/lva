<?php

namespace App\Http\Controllers\Admin\DataManagement;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Models\Season;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Laracasts\Flash\Flash;
use Session;

class SeasonsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $seasons = Season::paginate(15);

        return view('admin.data-management.seasons.index', compact('seasons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.data-management.seasons.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, ['season' => 'required', ]);

        Season::create($request->all());

        Flash::success('Season added!');

        return redirect('admin/data-management/seasons');
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
        $season = Season::findOrFail($id);

        return view('admin.data-management.seasons.show', compact('season'));
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
        $season = Season::findOrFail($id);

        return view('admin.data-management.seasons.edit', compact('season'));
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
        $this->validate($request, ['season' => 'required', ]);

        $season = Season::findOrFail($id);
        $season->update($request->all());

        Flash::success('Season updated!');

        return redirect('admin/data-management/seasons');
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
        if (false) {
            Season::destroy($id);

            Flash::success('Season deleted!');

        } else {
            Flash::error('Cannot delete season');
        }
        return redirect('admin/data-management/seasons');

    }

}
