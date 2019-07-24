<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VenueController extends Controller
{
    public function index(): View
    {
        return view('CRUD.venues.index', ['venues' => Venue::paginate(15)]);
    }

    public function create(): View
    {
        return view('CRUD.venues.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request,
            [
                'name' => 'required|unique:venues',
            ], [
                'name.required' => __('The name is required.'),
                'name.unique'   => __('The venue already exists.'),
            ]);

        Venue::create($request->only('name'));

        return redirect()->route('venues.index')->withToastSuccess(__('Venue added!'));
    }

    public function show(Venue $venue): View
    {
        return view('CRUD.venues.show', compact('venue'));
    }

    public function edit(Venue $venue): View
    {
        return view('CRUD.venues.edit', compact('venue'));
    }

    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $this->validate($request,
            [
                'name' => 'required|unique:venues,name,' . $venue->getId(),
            ], [
                'name.required' => __('The name is required.'),
                'name.unique'   => __('The venue already exists.'),
            ]);

        $venue->update($request->only('name'));

        return redirect()->route('venues.index')->withToastSuccess(__('Venue updated!'));
    }

    public function destroy(Venue $venue): RedirectResponse
    {
        $venue->delete();

        return redirect()->route('venues.index')->withToastSuccess(__('Venue deleted!'));
    }
}
