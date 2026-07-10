<?php

namespace App\Http\Controllers\Explore;

use App\Enums\Explorer\CommunityType;
use App\Enums\Explorer\LocatableType;
use App\Http\Controllers\Controller;
use App\Http\Resources\Explore\CircleSummaryResource;
use App\Models\Explorer\Circles\Circle;
use App\Services\Explore\ExploreQueryService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Explore index. URL query string is the single source of truth:
 *   ?circle=<id>&type=<TopTypeName>&community=<BottomTypeName>&view=browse|map
 * Mirrors the Livewire ExploreCommunities component; drill-down/filtering are
 * ordinary Inertia visits from the client (see useExploreNavigation).
 */
class ExploreController extends Controller
{
    public function index(Request $request, ExploreQueryService $svc): Response
    {
        $viewer = $request->user();

        // --- Read + resolve URL state (short enum names ↔ FQCNs) ---
        $typeParam      = $request->query('type');       // top section (null = Locations)
        $communityParam = $request->query('community');  // bottom section
        $circleId       = $request->integer('circle') ?: null;
        $view           = in_array($request->query('view'), ['browse', 'map'], true)
            ? $request->query('view')
            : 'browse';

        $topType    = CommunityType::fromName($typeParam);                    // ?CommunityType
        $bottomType = CommunityType::fromName($communityParam) ?? CommunityType::ThemeCommunity;

        // --- Resolve the geographic selection (breadcrumb may collapse it to
        //     national for a country-root or stale circle). ---
        $circle    = $svc->findCircle($circleId);
        $crumb     = $svc->breadcrumb($circle);
        $effective = $crumb['circleId'] === null ? null : $circle;

        // Canonical Explore URL for this view, threaded as ?from= on community links.
        $from = route('explore', array_filter([
            'circle'    => $crumb['circleId'],
            'type'      => $typeParam ?: null,
            'community' => $communityParam ?: null,
            'view'      => $view !== 'browse' ? $view : null,
        ], static fn ($v) => $v !== null && $v !== ''), absolute: false);

        $stamp = static function (Circle $c) use ($from): Circle {
            $c->explore_from = $from;

            return $c;
        };

        $topCommunities  = $svc->topCommunities($effective, $topType?->value, $viewer)->each($stamp);
        $typeCommunities = $svc->typeAtPlace($effective, $bottomType->value, $viewer)->each($stamp);
        $right           = $svc->rightColumnCircle($effective);
        $right?->setAttribute('explore_from', $from);

        return Inertia::render('Explore/Index', [
            'filters' => [
                'type'      => $topType?->name,      // null = Locations
                'community' => $bottomType->name,
                'circle'    => $crumb['circleId'],
                'view'      => $view,
            ],

            // Canonical URL of this Explore view — threaded as ?from= on
            // client-side search-result links (card links carry it already).
            'exploreUrl'        => $from,

            'breadcrumb'        => $crumb['trail'],
            'currentLevel'      => $this->currentLevel($effective),
            'isAtTerminalLevel' => LocatableType::tryFrom((string) $effective?->locatable_type)?->isTerminal() ?? false,

            'location' => [
                'communities' => CircleSummaryResource::collection($topCommunities),
                'countBelow'  => $svc->countBelow($effective, $topType?->value, $viewer),
            ],

            'rightColumnCircle' => $right ? new CircleSummaryResource($right) : null,

            'typeSection' => [
                'communities' => CircleSummaryResource::collection($typeCommunities),
                'countBelow'  => $svc->countBelow($effective, $bottomType->value, $viewer),
                'addLabel'    => $this->addLabel($bottomType),
            ],

            'meta' => [
                'top'    => $this->metaFor($topType),
                'bottom' => $this->metaFor($bottomType),
            ],

            'filterBars' => $this->filterBars(),
        ]);
    }

    private function currentLevel(?Circle $circle): string
    {
        return LocatableType::tryFrom((string) $circle?->locatable_type)?->label()
            ?? __('geographic.level.national');
    }

    /** "Add …" phrase for the bottom section (hardcoded article per type). */
    private function addLabel(CommunityType $type): string
    {
        return __('communities.add_label.'.match ($type) {
            CommunityType::Organisation   => 'organisation_community',
            CommunityType::ThemeCommunity => 'theme_community',
            CommunityType::Campaign       => 'campaign_community',
            CommunityType::Course         => 'course_community',
            CommunityType::Event          => 'event_community',
            default                       => 'default',
        });
    }

    /**
     * Section metadata (plural label / singular / icon). Null type = the
     * top-section "Locations" default.
     *
     * @return array{label: string, singular: string, icon: string}
     */
    private function metaFor(?CommunityType $type): array
    {
        if ($type === null) {
            return [
                'label'    => __('communities.plural.default'),
                'singular' => __('communities.singular.default'),
                'icon'     => '🌍',
            ];
        }

        return [
            'label'    => $type->plural(),
            'singular' => $type->singular(),
            'icon'     => $type->icon(),
        ];
    }

    /**
     * Pill definitions for the two filter bars. `value` is the URL param (enum
     * short name, or null for Locations).
     *
     * @return array{location: array<int, array>, community: array<int, array>}
     */
    private function filterBars(): array
    {
        $community = array_map(
            static fn (CommunityType $t) => ['value' => $t->name, 'label' => $t->plural(), 'icon' => $t->icon()],
            [
                CommunityType::ThemeCommunity,
                CommunityType::Organisation,
                CommunityType::Campaign,
                CommunityType::Course,
                CommunityType::Event,
            ],
        );

        return [
            'location'  => [
                ['value' => null, 'label' => __('communities.plural.locations'), 'icon' => '📍'],
            ],
            'community' => $community,
        ];
    }
}
