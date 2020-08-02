<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\PermissionsHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\DivisionResource;
use App\Models\Competition;
use App\Models\Division;
use App\Repositories\AccessibleDivisions;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class DivisionController extends Controller
{
    private AccessibleDivisions $accessibleDivisions;

    public function __construct(AccessibleDivisions $accessibleDivisions)
    {
        $this->accessibleDivisions = $accessibleDivisions;
    }

    public function all(Request $request): ResourceCollection
    {
        if ($request->has('competition')) {
            $this->accessibleDivisions->inCompetition(Competition::findOrFail($request->input('competition')));
        }

        return DivisionResource::collection($this->accessibleDivisions->get($request->user()));
    }

    public function get(Request $request, Division $division): JsonResource
    {
        if ($request->user()->can(PermissionsHelper::viewDivision($division))) {
            return new DivisionResource($division);
        }

        return new JsonResource([]);
    }
}
