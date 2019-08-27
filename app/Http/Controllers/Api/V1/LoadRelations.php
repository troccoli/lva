<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;

trait LoadRelations
{
    private function loadRelations(Request $request)
    {
        $relationsAllowed = [];
        if (property_exists($this, 'relationsAllowed')) {
            $relationsAllowed = $this->relationsAllowed;
        } elseif (method_exists($this, 'getRelationsAllowed')) {
            $relationsAllowed = $this->getRelationsAllowed();
        }

        collect($request->query('with', []))
            ->map(function (string $relation): string {
                return trim(strtolower($relation));
            })
            ->filter(function (string $relation) use ($relationsAllowed): bool {
                return collect($relationsAllowed)->contains($relation);
            })
            ->each(function (string $relation) use ($request): void {
                $this->resource->load($relation);
            });
    }
}
