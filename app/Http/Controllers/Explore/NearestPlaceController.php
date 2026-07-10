<?php

namespace App\Http\Controllers\Explore;

use App\Http\Controllers\Controller;
use App\Services\Explore\ExploreQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Resolves browser geolocation coordinates to the nearest MainPlace's Explore
 * URL — powers the "your home community" suggestion. JSON; one-shot after the
 * client obtains coordinates.
 */
class NearestPlaceController extends Controller
{
    public function __invoke(Request $request, ExploreQueryService $svc): JsonResponse
    {
        $data = $request->validate([
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
        ]);

        return response()->json([
            'suggestion' => $svc->nearestSuggestion((float) $data['lat'], (float) $data['lng']),
        ]);
    }
}
