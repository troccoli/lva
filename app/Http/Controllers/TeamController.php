<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Club;
use App\Models\Venue;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Club $club): View
    {
        $teams = Team::query()->inClub($club)->orderByName()->get();

        return view('CRUD.teams.index', compact('club', 'teams'));
    }

    public function create(Club $club): View
    {
        $venues = Venue::all();

        return view('CRUD.teams.create', compact('club', 'venues'));
    }

    public function store(Request $request, Club $club): RedirectResponse
    {
        $this->validate($request,
            [
                'club_id' => 'required|exists:clubs,id',
                'name' => [
                    'required',
                    Rule::unique('teams')->where(function (Builder $query) use ($club): Builder {
                        return $query->where('club_id', $club->getId());
                    }),
                ],
                'venue_id' => 'present|nullable|exists:venues,id'
            ], [
                'name.required' => __('The name is required.'),
                'name.unique'   => __('The team already exists in this club.'),
                'venue_id.present' => __('The venue is required.'),
                'venue_id.exists' => __('The venue does not exist.'),
            ]);

        Team::create($request->only('club_id', 'name', 'venue_id'));

        return redirect()
            ->route('teams.index', [$club])
            ->withToastSuccess(__('Team added!'));
    }

    public function edit(Club $club, Team $team): View
    {
        $venues = Venue::all();

        return view('CRUD.teams.edit', compact('club', 'team', 'venues'));
    }

    public function update(Request $request, Club $club, Team $team): RedirectResponse
    {
        $this->validate($request,
            [
                'name' => [
                    'required',
                    Rule::unique('teams')
                        ->ignore($team)
                        ->where(function (Builder $query) use ($team): Builder {
                            return $query->where('club_id', $team->getClub()->getId()
                            );
                        }),
                ],
                'venue_id' => 'present|nullable|exists:venues,id',
            ], [
                'name.required' => __('The name is required.'),
                'name.unique'   => __('The team already exists in this club.'),
                'venue_id.present' => __('The venue is required.'),
                'venue_id.exists' => __('The venue does not exist.'),
            ]);

        $team->update($request->only('name', 'venue_id'));

        return redirect()->route('teams.index', [$club])
            ->withToastSuccess(__('Team updated!'));
    }

    public function destroy(Club $club, Team $team): RedirectResponse
    {
        $team->delete();

        return redirect()
            ->route('teams.index', [$club])
            ->withToastSuccess(__('Team deleted!'));
    }
}