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
    public function index(Request $request)
    {
        $seasonId = $request->get('season_id');
        if (null !== $seasonId) {
            $season = Season::find($seasonId);
            if (null === $season) {
                return redirect()->route('seasons.index')->withToastError('The season does not exist!');
            }
        } else {
            $season = Season::query()->latest()->first();
            if (null === $season) {
                return redirect()->route('seasons.index')->withToastError('There are no seasons yet!');
            }
        }

        $competitions = Competition::query()->inSeason($season)->orderByName()->get();

        return view('CRUD.competitions.index', compact('season', 'competitions'));
    }

    public function create(Request $request): View
    {
        $seasonId = $request->get('season_id');
        if (null !== $seasonId) {
            $season = Season::find($seasonId);
            if (null === $season) {
                return redirect()->route('seasons.index')->withToastError('The season does not exist!');
            }
        } else {
            $season = Season::query()->latest()->first();
            if (null === $season) {
                return redirect()->route('seasons.index')->withToastError('There are no seasons yet!');
            }
        }

        return view('CRUD.competitions.create', compact('season'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request,
            [
                'season_id' => 'required|exists:seasons,id',
                'name'      => [
                    'required',
                    Rule::unique('competitions')->where(function (Builder $query) use ($request): Builder {
                        return $query->where('season_id', $request->get('season_id'));
                    }),
                ],
            ], [
                'name.required' => __('The name is required.'),
                'name.unique'   => __('The competition already exists in this season.'),
            ]);

        Competition::create($request->only('season_id', 'name'));

        return redirect()
            ->route('competitions.index', ['season_id' => $request->get('season_id')])
            ->withToastSuccess(__('Competition added!'));
    }

    public function edit(Request $request, Competition $competition): View
    {
        $seasonId = $request->get('season_id');
        if (null !== $seasonId) {
            $season = Season::find($seasonId);
            if (null === $season) {
                return redirect()->route('seasons.index')->withToastError('The season does not exist!');
            }
        } else {
            $season = Season::query()->latest()->first();
            if (null === $season) {
                return redirect()->route('seasons.index')->withToastError('There are no seasons yet!');
            }
        }

        return view('CRUD.competitions.edit', compact('season', 'competition'));
    }

    public function update(Request $request, Competition $competition): RedirectResponse
    {
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

        return redirect()->route('competitions.index', ['season_id' => $competition->getSeason()->getId()])
            ->withToastSuccess(__('Competition updated!'));
    }

    public function destroy(Competition $competition): RedirectResponse
    {
        $season = $competition->getSeason();
        $competition->delete();

        return redirect()
            ->route('competitions.index', ['season_id' => $season->getId()])
            ->withToastSuccess(__('Competition deleted!'));
    }
}
