<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\V1\LoadRelations;
use App\Models\Competition;
use Illuminate\Http\Resources\Json\JsonResource;

class CompetitionResource extends JsonResource
{
    use LoadRelations;

    protected $relationsAllowed = ['season', 'divisions'];

    public function toArray($request): array
    {
        $this->loadRelations($request);

        /** @var Competition $competition */
        $competition = $this->resource;

        return [
            'id'        => $competition->getId(),
            'name'      => $competition->getName(),
            'season'    => $this->whenLoaded('season', function () use ($competition): SeasonResource {
                return new SeasonResource($competition->getSeason());
            }),
            'divisions' => DivisionResource::collection($this->whenLoaded('divisions')),
        ];
    }
}
