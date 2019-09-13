<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\FixtureResource;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Fixture;
use App\Models\Season;
use App\Models\Team;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FixturesController extends Controller
{
    public function all(Request $request): ResourceCollection
    {
        $query = Fixture::query();

        if ($request->has('season')) {
            /** @var Season $season */
            $season = Season::findOrFail($request->get('season'));
            $divisionsInSeason = $season->getCompetitions()->reduce(function (
                Collection $divisions,
                Competition $competition
            ): Collection {
                return $divisions->merge($competition->getDivisions());
            }, new Collection())
                ->pluck('id');
            $query->whereIn('division_id', $divisionsInSeason);
        } elseif ($request->has('competition')) {
            /** @var Competition $competition */
            $competition = Competition::findOrFail($request->get('competition'));
            $divisionsInCompetition = $competition->getDivisions()->pluck('id');
            $query->whereIn('division_id', $divisionsInCompetition);
        } elseif ($request->has('division')) {
            /** @var Division $division */
            $division = Division::findOrFail($request->get('division'));
            $query->where('division_id', $division->getId());
        }

        if ($request->has('on')) {
            try {
                $date = Carbon::createFromFormat('Y-m-d', $request->get('on'));
            } catch (\InvalidArgumentException $e) {
                throw new ModelNotFoundException();
            }
            $query->where('match_date', $date->setTime(0, 0, 0));
        }

        if ($request->has('venue')) {
            /** @var Venue $venue */
            $venue = Venue::findOrFail($request->get('venue'));
            $query->where('venue_id', $venue->getId());
        }

        if ($request->has('team')) {
            /** @var Team $team */
            $team = Team::findOrFail($request->get('team'));
            $query->where(function (Builder $query) use ($team): Builder {
                return $query->where('home_team_id', $team->getId())
                    ->orWhere('away_team_id', $team->getId());
            });
        } elseif ($request->has('homeTeam')) {
            /** @var Team $homeTeam */
            $homeTeam = Team::findOrFail($request->get('homeTeam'));
            $query->where('home_team_id', $homeTeam->getId());
        } elseif ($request->has('awayTeam')) {
            /** @var Team $awayTeam */
            $awayTeam = Team::findOrFail($request->get('awayTeam'));
            $query->where('away_team_id', $awayTeam->getId());
        }

        $query->with(['division', 'venue', 'homeTeam', 'awayTeam']);

        $perPage = $request->get('perPage', 10);

        return FixtureResource::collection($query->paginate($perPage));
    }
}
