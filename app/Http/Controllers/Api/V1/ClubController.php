<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClubResource;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ClubController extends Controller
{
    public function all(Request $request): ResourceCollection
    {
        return ClubResource::collection(Club::all());
    }

    public function get(Request $request, Club $club): ClubResource
    {
        return new ClubResource($club);
    }
}
