<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\V1\LoadRelations;
use App\Models\Competition;
use App\Repositories\AccessibleDivisions;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class CompetitionResource extends JsonResource
{
    use LoadRelations;

    protected array $relationsAllowed = ['season', 'divisions'];
    protected AccessibleDivisions $accessibleDivisions;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->accessibleDivisions = resolve(AccessibleDivisions::class);
    }

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
            'divisions' => DivisionResource::collection(
                $this->whenLoaded('divisions', function () use ($request, $competition): Collection {
                    return $this->accessibleDivisions->inCompetition($competition)->get($request->user());
                })
            ),
        ];
    }
}
