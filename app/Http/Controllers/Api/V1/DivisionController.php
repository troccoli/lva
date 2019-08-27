<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\DivisionResource;
use App\Models\Competition;
use App\Models\Division;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

class DivisionController extends Controller
{
    public function all(Request $request, Competition $competition): ResourceCollection
    {
        $query = Division::query();

        if ($request->has('competition')) {
            $query->inCompetition(Competition::findOrFail($request->get('competition')));
        }

        return DivisionResource::collection($query->get());
    }

    public function get(Request $request, Division $division): DivisionResource
    {
        return new DivisionResource($division);
    }
}
