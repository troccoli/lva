<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\V1\LoadRelations;
use App\Models\Division;
use Illuminate\Http\Resources\Json\JsonResource;

class DivisionResource extends JsonResource
{
    use LoadRelations;

    protected $relationsAllowed = ['competition', 'teams'];

    public function toArray($request): array
    {
        $this->loadRelations($request);

        /** @var Division $division */
        $division = $this->resource;

        return [
            'id'             => $division->getId(),
            'name'           => $division->getName(),
            'display_order'  => $division->getOrder(),
            'competition' => $this->whenLoaded('competition', function () use ($division): CompetitionResource {
                return new CompetitionResource($division->getCompetition());
            }),
            'teams'          => TeamResource::collection($this->whenLoaded('teams')),
        ];
    }
}
