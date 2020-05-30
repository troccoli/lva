<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Season;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CompetitionController extends Controller
{
    public function index(Season $season)
    {
        $this->authorize('viewAny', [Competition::class, $season]);

        $competitions = Competition::query()->inSeason($season)->orderByName()->get();

        return view('CRUD.competitions.index', compact('season', 'competitions'));
    }

    public function create(Season $season): View
    {
        $this->authorize('create', [Competition::class, $season]);

        return view('CRUD.competitions.create', compact('season'));
    }

    public function store(Request $request, Season $season): RedirectResponse
    {
        $this->authorize('create', [Competition::class, $season]);

        $this->validate($request,
            [
                'name' => [
                    'required',
                    Rule::unique('competitions')->where(function (Builder $query) use ($season): Builder {
                        return $query->where('season_id', $season->getId());
                    }),
                ],
            ], [
                'name.required' => __('The name is required.'),
                'name.unique'   => __('The competition already exists in this season.'),
            ]);

        Competition::create([
            'season_id' => $season->getId(),
            'name'      => $request->get('name'),
        ]);

        return redirect()
            ->route('competitions.index', [$season])
            ->withToastSuccess(__('Competition added!'));
    }

    public function edit(Season $season, Competition $competition): View
    {
        $this->authorize('update', $competition);

        return view('CRUD.competitions.edit', compact('season', 'competition'));
    }

    public function update(Request $request, Season $season, Competition $competition): RedirectResponse
    {
        $this->authorize('update', $competition);

        $this->validate($request,
            [
                'name' => [
                    'required',
                    Rule::unique('competitions')
                        ->ignore($competition)
                        ->where(function (Builder $query) use ($competition): Builder {
                            return $query->where('season_id', $competition->getSeason()->getId()
                            );
                        }),
                ],
            ], [
                'name.required' => __('The name is required.'),
                'name.unique'   => __('The competition already exists in this season.'),
            ]);

        $competition->update($request->only('name'));

        return redirect()->route('competitions.index', [$season])
            ->withToastSuccess(__('Competition updated!'));
    }

    public function destroy(Season $season, Competition $competition): RedirectResponse
    {
        $this->authorize('delete', $competition);

        $competition->delete();

        return redirect()
            ->route('competitions.index', [$season])
            ->withToastSuccess(__('Competition deleted!'));
    }
}
