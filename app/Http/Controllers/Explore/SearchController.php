<?php

namespace App\Http\Controllers\Explore;

use App\Enums\Explorer\CommunityType;
use App\Http\Controllers\Controller;
use App\Models\Explorer\Circles\Circle;
use App\Services\Explore\ExploreQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Autocomplete search for the Explore search overlay. JSON (not Inertia) so
 * keystroke queries don't push history — mirrors the Livewire SearchOverlay.
 */
class SearchController extends Controller
{
    public function __invoke(Request $request, ExploreQueryService $svc): JsonResponse
    {
        $query = (string) $request->query('q', '');
        $type  = CommunityType::fromName($request->query('type'))?->value;

        // Optional return-to-Explore URL, threaded onto result links.
        $from = $request->query('from');
        $from = is_string($from) && str_starts_with($from, '/explore') ? $from : null;

        $results = $svc->search($query, $type, $request->user())
            ->map(fn (Circle $c) => [
                'id'            => $c->id,
                'name'          => $c->name,
                'location'      => $c->locatable?->name,
                'communityType' => CommunityType::tryFrom((string) $c->circleable_type)?->name,
                'badge'         => CommunityType::tryFrom((string) $c->circleable_type)?->singular()
                    ?? __('communities.singular.default'),
                'url'           => $from
                    ? route('communities.show', ['circle' => $c->id, 'from' => $from], absolute: false)
                    : route('communities.show', ['circle' => $c->id], absolute: false),
            ])
            ->values();

        return response()->json(['results' => $results]);
    }
}
