<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClubController extends Controller
{
    public function index(): View
    {
        return view('CRUD.clubs.index', ['clubs' => Club::orderBy('name')->paginate(15)]);
    }

    public function create(): View
    {
        return view('CRUD.clubs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request,
            [
                'name' => 'required|unique:clubs',
            ], [
                'name.required' => __('The name is required.'),
                'name.unique'   => __('The club already exists.'),
            ]);

        Club::create($request->only('name'));

        return redirect()->route('clubs.index')->withToastSuccess(__('Club added!'));
    }

    public function edit(Club $club): View
    {
        return view('CRUD.clubs.edit', compact('club'));
    }

    public function update(Request $request, Club $club): RedirectResponse
    {
        $this->validate($request,
            [
                'name' => 'required|unique:clubs,name,' . $club->getId(),
            ], [
                'name.required' => __('The name is required.'),
                'name.unique'   => __('The club already exists.'),
            ]);

        $club->update($request->only('name'));

        return redirect()->route('clubs.index')->withToastSuccess(__('Club updated!'));
    }

    public function destroy(Club $club): RedirectResponse
    {
        $club->delete();

        return redirect()->route('clubs.index')->withToastSuccess(__('Club deleted!'));
    }
}
