<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\V1\LoadRelations;
use App\Models\Team;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    use LoadRelations;

    protected $relationsAllowed = ['club', 'venue', 'divisions'];

    public function toArray($request): array
    {
        $this->loadRelations($request);

        /** @var Team $team */
        $team = $this->resource;

        return [
            'id'        => $team->getId(),
            'name'      => $team->getName(),
            'club'      => $this->whenLoaded('club', function () use ($team): ClubResource {
                return new ClubResource($team->getClub());
            }),
            'venue'     => $this->whenLoaded('venue', function () use ($team): VenueResource {
                return new VenueResource($team->getVenue());
            }),
            'divisions' => DivisionResource::collection($this->whenLoaded('divisions')),
        ];
    }
}
