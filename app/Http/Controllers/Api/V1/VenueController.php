<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class VenueController extends Controller
{
    public function all(Request $request): ResourceCollection
    {
        return VenueResource::collection(Venue::all());
    }

    public function get(Request $request, Venue $venue): VenueResource
    {
        return new VenueResource($venue);
    }
}
