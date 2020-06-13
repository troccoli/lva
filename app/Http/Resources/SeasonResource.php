<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\V1\LoadRelations;
use App\Models\Season;
use App\Repositories\AccessibleCompetitions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\JsonResource;

class SeasonResource extends JsonResource
{
    use LoadRelations;

    protected $relationsAllowed = ['competitions'];
    private AccessibleCompetitions $accessibleCompetitions;

    public function __construct($resource)
    {
        parent::__construct($resource);

        $this->accessibleCompetitions = resolve(AccessibleCompetitions::class);
    }

    /**
     * @param \Illuminate\Http\Request $request
     */

    public function toArray($request): array
    {
        $this->loadRelations($request);

        /** @var Season $season */
        $season = $this->resource;

        return [
            'id' => $season->getId(),
            'name' => $season->getName(),
            'competitions' => CompetitionResource::collection(
                $this->whenLoaded('competitions', function () use ($request, $season): Collection {
                    return $this->accessibleCompetitions->inSeason($season)->get($request->user());
                })),
        ];
    }
}
