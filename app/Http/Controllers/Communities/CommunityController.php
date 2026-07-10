<?php

namespace App\Http\Controllers\Communities;

use App\Http\Controllers\Controller;
use App\Http\Resources\Explore\CircleDetailResource;
use App\Models\Explorer\Circles\Circle;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Community (circle) detail page. Public for now — permissions later. Mirrors
 * the Livewire CommunityPage.
 */
class CommunityController extends Controller
{
    public function show(Request $request, Circle $circle): Response
    {
        $circle->load(['circleable', 'locatable', 'services']);

        return Inertia::render('Communities/Show', [
            'circle'         => new CircleDetailResource($circle),
            'administrators' => $circle->administrators()->pluck('name')->values(),
            'backUrl'        => $this->resolveBackUrl($request->query('from')),
        ]);
    }

    /** Only accept an internal /explore path as the back target (no open redirects). */
    private function resolveBackUrl(mixed $from): string
    {
        if (is_string($from) && str_starts_with($from, '/explore')) {
            return $from;
        }

        return route('explore', absolute: false);
    }
}
