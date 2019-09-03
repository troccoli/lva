<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\V1\LoadRelations;
use App\Models\Club;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubResource extends JsonResource
{
    use LoadRelations;

    protected $relationsAllowed = ['venue', 'teams'];

    public function toArray($request): array
    {
        $this->loadRelations($request);

        /** @var Club $club */
        $club = $this->resource;

        return [
            'id'    => $club->getId(),
            'name'  => $club->getName(),
            'venue' => $this->whenLoaded('venue', function () use ($club): VenueResource {
                return new VenueResource($club->getVenue());
            }),
            'teams' => TeamResource::collection($this->whenLoaded('teams')),
        ];
    }
}
