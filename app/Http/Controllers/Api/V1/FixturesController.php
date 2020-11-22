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
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;

class FixturesController extends Controller
{
    public function all(Request $request): ResourceCollection
    {
        $this->validate($request, [
            'season' => 'sometimes|exists:seasons,id',
            'competition' => 'sometimes|exists:competitions,id',
            'division' => 'sometimes|exists:divisions,id',
            'at' => 'sometimes|exists:venues,id',
            'on' => 'sometimes|date_format:Y-m-d',
            'team' => 'sometimes|exists:teams,id',
            'homeTeam' => 'sometimes|exists:teams,id',
            'awayTeam' => 'sometimes|exists:teams,id',
        ]);

        $query = Fixture::query();

        if ($request->has('season')) {
            $query->inSeason(Season::findOrFail($request->input('season')));
        } elseif ($request->has('competition')) {
            $query->inCompetition(Competition::findOrFail($request->input('competition')));
        } elseif ($request->has('division')) {
            $query->inDivision(Division::findOrFail($request->input('division')));
        }

        if ($request->has('on')) {
            $query->on(Carbon::parse($request->input('on')));
        }

        if ($request->has('at')) {
            $query->at(Venue::findOrFail($request->input('at')));
        }

        if ($request->has('team')) {
            $query->forTeam(Team::findOrFail($request->input('team')));
        } elseif ($request->has('homeTeam')) {
            $query->forHomeTeam(Team::findOrFail($request->input('homeTeam')));
        } elseif ($request->has('awayTeam')) {
            $query->forAwayTeam(Team::findOrFail($request->input('awayTeam')));
        }

        if ($request->hasAny(['page', 'perPage'])) {
            $perPage = (int) $request->get('perPage', 10);

            return FixtureResource::collection($query->paginate($perPage));
        }

        return FixtureResource::collection(($query->get()));
    }
}
