<?php

namespace App\Http\Resources\Explore;

use App\Enums\Explorer\CommunityType;
use App\Enums\Explorer\LocatableType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Summary shape for a circle in Explore lists and card grids. Consumed by the
 * React LocationColumn / CommunityCard. All display strings (icons, badges,
 * labels) are resolved server-side so the client never re-implements the enum
 * or geographic match() logic.
 *
 * @property-read \App\Models\Explorer\Circles\Circle $resource
 */
class CircleSummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $type = CommunityType::tryFrom((string) $this->circleable_type);

        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'displayName' => $this->locatable?->name ?? $this->name,
            'description' => $this->description,
            'status'      => $this->status?->value,

            // "Also here" badge — set on associated circles by the query service.
            'alsoHere'    => (bool) ($this->also_here ?? false),

            'communityType' => $type ? [
                'name'     => $type->name,
                'icon'     => $type->icon(),
                'singular' => $type->singular(),
            ] : null,

            'level' => $this->when(
                $this->locatable_type !== null,
                fn () => $this->level(),
            ),

            // Detail-page URL, carrying ?from= (the Explore view to return to)
            // when the query service stamped it onto the model.
            'url' => $this->detailUrl(),
        ];
    }

    /**
     * Geographic level metadata, with both badge variants (list vs card)
     * pre-localised so each surface picks the one it needs.
     *
     * @return array{key: string, badgeList: string, badgeCard: string, isTerminal: bool}
     */
    private function level(): array
    {
        $key = class_basename((string) $this->locatable_type);
        $isMetro = (bool) ($this->locatable?->metropolis ?? false);

        return [
            'key'        => $key,
            'badgeList'  => __('geographic.badge_list.'.match ($key) {
                'Country'              => 'country',
                'Province'             => 'province',
                'DistrictMunicipality' => 'dm',
                'LocalMunicipality'    => 'local_municipality',
                'MainPlace'            => 'main_place',
                'City'                 => $isMetro ? 'metro' : 'city',
                default                => 'main_place',
            }),
            'badgeCard'  => __('geographic.badge_card.'.match ($key) {
                'Country'              => 'national',
                'Province'             => 'provincial',
                'DistrictMunicipality' => 'dm',
                'LocalMunicipality'    => 'lm',
                'MainPlace'            => 'main_place',
                'City'                 => $isMetro ? 'metro' : 'city',
                default                => 'main_place',
            }),
            'isTerminal' => LocatableType::tryFrom((string) $this->locatable_type)?->isTerminal() ?? false,
        ];
    }

    private function detailUrl(): string
    {
        $from = $this->explore_from ?? null;

        return $from
            ? route('communities.show', ['circle' => $this->id, 'from' => $from], absolute: false)
            : route('communities.show', ['circle' => $this->id], absolute: false);
    }
}
