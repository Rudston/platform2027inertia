<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * Translation namespaces exposed to the client as a flat bag. The React
     * layer reads these via useTrans() so it never re-implements i18n; the
     * existing lang/{en,pt} files remain the single source of strings.
     *
     * @var list<string>
     */
    private const TRANSLATION_GROUPS = [
        'explore', 'communities', 'ui', 'geographic', 'navigation',
    ];

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),

            'appName' => config('app.name'),
            'locale'  => app()->getLocale(),

            // Flat translation bag for the client (see useTrans()).
            'translations' => $this->translations(),

            // Transient flash messages.
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],

            // TODO(merge → Mobilize v2): the host app additionally shares
            //   'auth', 'companyName' (white-label), 'enabledFeatures',
            //   'permissions'. Wire these in via the target's HandleInertiaRequests.
        ];
    }

    /**
     * Load the exposed translation groups for the active locale into a
     * flat "group.key" => value map.
     *
     * @return array<string, string>
     */
    private function translations(): array
    {
        $bag = [];

        foreach (self::TRANSLATION_GROUPS as $group) {
            $lines = Lang::get($group);

            if (! is_array($lines)) {
                continue;
            }

            foreach (Arr::dot($lines, $group.'.') as $key => $value) {
                $bag[$key] = $value;
            }
        }

        return $bag;
    }
}
