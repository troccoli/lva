<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\V1\LoadRelations;
use App\Models\Venue;
use Illuminate\Http\Resources\Json\JsonResource;

class VenueResource extends JsonResource
{
    use LoadRelations;

    protected $relationsAllowed = ['clubs'];

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function toArray($request): array
    {
        $this->loadRelations($request);

        /** @var Venue $venue */
        $venue = $this->resource;

        return [
            'id'    => $venue->getId(),
            'name'  => $venue->getName(),
            'clubs' => ClubResource::collection($this->whenLoaded('clubs')),
        ];
    }
}
