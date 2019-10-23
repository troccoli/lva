<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeasonResource;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SeasonController extends Controller
{
    public function all(Request $request): ResourceCollection
    {
        return SeasonResource::collection(Season::all()->sortByDesc('year'));
    }

    public function get(Request $request, Season $season): SeasonResource
    {
        return new SeasonResource($season);
    }
}
