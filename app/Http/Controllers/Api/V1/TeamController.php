<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TeamController extends Controller
{
    public function all(Request $request): ResourceCollection
    {
        return TeamResource::collection(Team::all());
    }

    public function get(Request $request, Team $team): TeamResource
    {
        return new TeamResource($team);
    }
}
