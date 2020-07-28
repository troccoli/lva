<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Club::class, 'club');
    }

    public function index(): View
    {
        return view('CRUD.clubs.index', ['clubs' => Club::orderBy('name')->paginate(15)]);
    }

    public function create(): View
    {
        $venues = Venue::all();

        return view('CRUD.clubs.create', compact('venues'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate(
            $request,
            [
                'name' => 'required|unique:clubs',
                'venue_id' => 'present|nullable|exists:venues,id'
            ],
            [
                'name.required' => __('The name is required.'),
                'name.unique'   => __('The club already exists.'),
                'venue_id.present' => __('The venue is required.'),
                'venue_id.exists' => __('The venue does not exist.'),
            ]
        );

        Club::create($request->only('name', 'venue_id'));

        return redirect()->route('clubs.index')->withToastSuccess(__('Club added!'));
    }

    public function edit(Club $club): View
    {
        $venues = Venue::all();

        return view('CRUD.clubs.edit', compact('club', 'venues'));
    }

    public function update(Request $request, Club $club): RedirectResponse
    {
        $this->validate(
            $request,
            [
                'name' => 'required|unique:clubs,name,' . $club->getId(),
                'venue_id' => 'present|nullable|exists:venues,id',
            ],
            [
                'name.required' => __('The name is required.'),
                'name.unique'   => __('The club already exists.'),
                'venue_id.present' => __('The venue is required.'),
                'venue_id.exists' => __('The venue does not exist.'),
            ]
        );

        $club->update($request->only('name', 'venue_id'));

        return redirect()->route('clubs.index')->withToastSuccess(__('Club updated!'));
    }

    public function destroy(Club $club): RedirectResponse
    {
        $club->delete();

        return redirect()->route('clubs.index')->withToastSuccess(__('Club deleted!'));
    }
}
