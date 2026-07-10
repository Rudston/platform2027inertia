<?php

namespace App\Http\Resources\Explore;

use App\Enums\Explorer\CommunityType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Full shape for the community detail page (Communities/Show). Expects the
 * circle loaded with circleable, locatable, and services. Administrators are a
 * derived (method-based) collection, passed as a separate page prop.
 *
 * @property-read \App\Models\Explorer\Circles\Circle $resource
 */
class CircleDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $type = CommunityType::tryFrom((string) $this->circleable_type);

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'status'      => $this->status?->value,
            'locationName' => $this->locatable?->name,
            'memberCount' => 0, // placeholder — membership system is separate

            'communityType' => $type ? [
                'name'     => $type->name,
                'icon'     => $type->icon(),
                'singular' => $type->singular(),
            ] : null,

            // Active services (names only, for the chips).
            'services' => $this->services
                ->filter(fn ($s) => (bool) ($s->pivot->is_active ?? false))
                ->pluck('name')
                ->values(),
        ];
    }
}
