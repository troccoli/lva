<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\PermissionsHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompetitionResource;
use App\Models\Competition;
use App\Models\Season;
use App\Repositories\AccessibleCompetitions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CompetitionController extends Controller
{
    private AccessibleCompetitions $accessibleCompetitions;

    public function __construct(AccessibleCompetitions $accessibleCompetitions)
    {
        $this->accessibleCompetitions = $accessibleCompetitions;
    }

    public function all(Request $request): ResourceCollection
    {
        if ($request->has('season')) {
            $this->accessibleCompetitions->inSeason(Season::findOrFail($request->input('season')));
        }

        return CompetitionResource::collection($this->accessibleCompetitions->get($request->user()));
    }

    public function get(Request $request, Competition $competition): JsonResource
    {
        if ($request->user()->can(PermissionsHelper::viewCompetition($competition))) {
            return new CompetitionResource($competition);
        }

        return new JsonResource([]);
    }
}
