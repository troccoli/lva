<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laracasts\Flash\Flash;

class SeasonController extends Controller
{
    public function index(): View
    {
        return view('CRUD.seasons.index', ['seasons' => Season::orderBy('year', 'desc')->paginate(5)]);
    }

    public function create(): View
    {
        return view('CRUD.seasons.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request,
            [
                'year' => 'required|integer|unique:seasons',
            ], [
                'year.required' => __('The year is required.'),
                'year.integer' => __('The year is not valid.'),
                'year.unique' => __('The season already exists.'),
            ]);

        Season::create($request->only('year'));

        Flash::success('Season added!');

        return redirect()->route('seasons.index');
    }

    public function edit(Season $season): View
    {
        return view('CRUD.seasons.edit', compact('season'));
    }

    public function update(Request $request, Season $season): RedirectResponse
    {
        $this->validate($request,
            [
                'year' => 'required|integer|unique:seasons,year,' . $season->getId(),
            ], [
                'year.required' => __('The year is required.'),
                'year.integer' => __('The year is not valid.'),
                'year.unique' => __('The season already exists.'),
            ]);

        $season->update($request->only('year'));

        Flash::success('Season updated!');

        return redirect()->route('seasons.index');
    }

    public function destroy(Season $season): RedirectResponse
    {
        $season->delete();

        return redirect()->route('seasons.index');
    }
}
