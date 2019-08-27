<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompetitionResource;
use App\Models\Competition;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompetitionController extends Controller
{
    public function all(Request $request): ResourceCollection
    {
        $query = Competition::query();

        if ($request->has('season')) {
            $query->inSeason(Season::findOrFail($request->get('season')));
        }

        return CompetitionResource::collection($query->get());
    }

    public function get(Request $request, Competition $competition): CompetitionResource
    {
        return new CompetitionResource($competition);
    }
}
