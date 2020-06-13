<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\PermissionsHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\SeasonResource;
use App\Models\Season;
use App\Repositories\AccessibleSeasons;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SeasonController extends Controller
{
    private AccessibleSeasons $accessibleSeasons;

    public function __construct(AccessibleSeasons $accessibleSeasons)
    {
        $this->accessibleSeasons = $accessibleSeasons;
    }

    public function all(Request $request): ResourceCollection
    {
        return SeasonResource::collection($this->accessibleSeasons->get($request->user())->sortByDesc('year'));
    }

    public function get(Request $request, Season $season): JsonResource
    {
        if ($request->user()->can(PermissionsHelper::viewSeason($season))) {
            return new SeasonResource($season);
        }

        return new JsonResource([]);
    }
}
