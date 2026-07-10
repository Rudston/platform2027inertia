<?php

namespace App\Services\Explore;

use App\Enums\Explorer\CommunityType;
use App\Enums\Explorer\LocatableType;
use App\Models\Explorer\Circles\Circle;
use App\Models\Explorer\Demography\CoordinateData;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Query logic for the Explore feature, lifted verbatim from the Livewire
 * ExploreCommunities computed properties so behaviour is identical. Controllers
 * stay thin; this is the single place the Explore reads happen.
 */
class ExploreQueryService
{
    /** Eager-loads shared by the list/card queries. */
    private const WITH = ['circleable', 'locatable', 'services'];

    // TODO(multi-country): the national anchor is hardcoded to South Africa.
    private const SOUTH_AFRICA_ID = 191;

    /** Root (country) LocationCommunity circle id, or null if not seeded. */
    public function countryCircleId(): ?int
    {
        return Circle::query()
            ->whereNull('parent_id')
            ->where('circleable_type', CommunityType::LocationCommunity->value)
            ->value('id');
    }

    public function findCircle(?int $id): ?Circle
    {
        return $id ? Circle::with('locatable')->find($id) : null;
    }

    /**
     * Circle shown in the top-section right column: the selected circle, or the
     * national (country) circle when nothing is selected.
     */
    public function rightColumnCircle(?Circle $selected): ?Circle
    {
        if ($selected) {
            return $selected;
        }

        $countryId = $this->countryCircleId();

        return $countryId ? Circle::with(self::WITH)->find($countryId) : null;
    }

    /**
     * Top-section list. Location mode (no type, or LocationCommunity): the
     * drill-down children of the selected circle plus approved "also here"
     * associations. Otherwise: communities of $type at the current place.
     *
     * @return Collection<int, Circle>
     */
    public function topCommunities(?Circle $circle, ?string $type, ?User $viewer): Collection
    {
        $isLocationMode = $type === null || $type === CommunityType::LocationCommunity->value;

        if (! $isLocationMode) {
            return $this->typeAtPlace($circle, $type, $viewer);
        }

        // At national level (no circle), list the country's children (provinces).
        $parentId = $circle?->id ?? $this->countryCircleId();

        if ($parentId === null) {
            return collect();
        }

        $children = Circle::query()
            ->where('circleable_type', CommunityType::LocationCommunity->value)
            ->where('parent_id', $parentId)
            ->visibleTo($viewer)
            ->with(self::WITH)
            ->orderBy('name')
            ->get();

        $children->each(fn (Circle $c) => $c->also_here = false);

        // Merge circles that approved-associated themselves here, badged "also
        // here". Dedupe by id — native children win.
        $current = $circle ?? Circle::find($parentId);

        $associated = $current
            ? $current->approvedAssociatedBy()
                ->visibleTo($viewer)
                ->with(self::WITH)
                ->orderBy('circles.name')
                ->get()
            : collect();

        $associated->each(fn (Circle $c) => $c->also_here = true);

        $childIds = $children->pluck('id')->all();
        $extra = $associated->reject(fn (Circle $c) => in_array($c->id, $childIds, true));

        return $children->concat($extra)->values();
    }

    /**
     * Communities of a given type located at the current place (or Country /
     * South Africa at national level). Used by the top section's non-location
     * mode and by the bottom section.
     *
     * @return Collection<int, Circle>
     */
    public function typeAtPlace(?Circle $circle, ?string $type, ?User $viewer): Collection
    {
        if ($type === null) {
            return collect();
        }

        [$locatableType, $locatableId] = $circle
            ? [(string) $circle->locatable_type, (int) $circle->locatable_id]
            : [LocatableType::Country->value, self::SOUTH_AFRICA_ID];

        return Circle::query()
            ->where('circleable_type', $type)
            ->where('locatable_type', $locatableType)
            ->where('locatable_id', $locatableId)
            ->visibleTo($viewer)
            ->with(self::WITH)
            ->orderBy('name')
            ->get();
    }

    /** Count of $type communities in descendants of the selected circle. */
    public function countBelow(?Circle $circle, ?string $type, ?User $viewer): int
    {
        if ($circle === null || $type === null || ! $circle->path) {
            return 0;
        }

        return Circle::query()
            ->where('circleable_type', $type)
            ->where('path', 'like', $circle->path.'/%')
            ->visibleTo($viewer)
            ->count();
    }

    /**
     * Geographic breadcrumb for a circle, always starting at South Africa (id
     * null). Mirrors the Livewire buildBreadcrumbForSelectedCircle(); returns
     * the (possibly corrected) circle id alongside the trail, since a country-
     * root or stale circle collapses to national level.
     *
     * @return array{circleId: ?int, trail: array<int, array{id: ?int, name: string}>}
     */
    public function breadcrumb(?Circle $circle): array
    {
        $trail = [['id' => null, 'name' => 'South Africa']];

        if ($circle === null) {
            return ['circleId' => null, 'trail' => $trail];
        }

        foreach ($circle->ancestors() as $ancestor) {
            if ($ancestor->parent_id === null) {
                continue; // country root is represented by "South Africa" (null)
            }
            $ancestor->loadMissing('locatable');
            $trail[] = ['id' => $ancestor->id, 'name' => $ancestor->locatable?->name ?? $ancestor->name];
        }

        if ($circle->parent_id === null) {
            // The selected circle IS the country root → national level.
            return ['circleId' => null, 'trail' => $trail];
        }

        $trail[] = ['id' => $circle->id, 'name' => $circle->locatable?->name ?? $circle->name];

        return ['circleId' => $circle->id, 'trail' => $trail];
    }

    /**
     * Search circles by name (min 2 chars), optionally constrained to a
     * community type. Mirrors the Livewire SearchOverlay.
     *
     * @return Collection<int, Circle>
     */
    public function search(string $query, ?string $type, ?User $viewer): Collection
    {
        $query = trim($query);

        if (mb_strlen($query) < 2) {
            return collect();
        }

        return Circle::query()
            ->where('name', 'like', '%'.$query.'%')
            ->when($type, fn ($q) => $q->where('circleable_type', $type))
            ->visibleTo($viewer)
            ->with(['circleable', 'locatable'])
            ->orderBy('name')
            ->limit(10)
            ->get();
    }

    /**
     * Nearest MainPlace suggestion for browser coordinates: its Explore URL and
     * name, or null if nothing resolves.
     *
     * @return array{url: string, name: string}|null
     */
    public function nearestSuggestion(float $latitude, float $longitude): ?array
    {
        $mainPlace = CoordinateData::nearest($latitude, $longitude)?->getMainPlace();
        $url = $mainPlace?->explorerLocationCommunityUrl();

        if (! $url) {
            return null;
        }

        return ['url' => $url, 'name' => $mainPlace->name];
    }
}
