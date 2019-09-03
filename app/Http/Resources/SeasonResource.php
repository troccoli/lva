<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\V1\LoadRelations;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeasonResource extends JsonResource
{
    use LoadRelations;

    protected $relationsAllowed = ['competitions'];

    /**
     * @param \Illuminate\Http\Request $request
     */

    public function toArray($request): array
    {
        $this->loadRelations($request);

        /** @var Season $season */
        $season = $this->resource;

        return [
            'id'   => $season->getId(),
            'name' => $season->getName(),
            'competitions' => CompetitionResource::collection($this->whenLoaded('competitions')),
        ];
    }
}
