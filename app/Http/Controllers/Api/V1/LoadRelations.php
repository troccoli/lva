<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

trait LoadRelations
{
    private function loadRelations(Request $request)
    {
        if (property_exists($this, 'relationsAllowed')) {
            $relationsAllowed = collect($this->relationsAllowed);
        } elseif (method_exists($this, 'getRelationsAllowed')) {
            $relationsAllowed = $this->getRelationsAllowed();
        } else {
            $relationsAllowed = collect([]);
        }

        $relationsRequested = collect($request->query('with', []))
            ->map(function (string $relation): string {
                return trim(strtolower($relation));
            });

        $relationsAllowed->each(function (string $relation): void {
            $this->resource->unsetRelation($relation);
        })->intersect($relationsRequested)->each(function (string $relation): void {
            $this->resource->load($relation);
        });
    }
}
