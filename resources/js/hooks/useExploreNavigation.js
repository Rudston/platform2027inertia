import { router } from '@inertiajs/react';

/**
 * Explore state lives entirely in the URL query string (?circle&type&community
 * &view) — this is the Inertia equivalent of Livewire's #[Url]. Each action is
 * an Inertia visit to the current page with a patched query, preserving React
 * state and scroll. The server rebuilds everything (incl. the breadcrumb) from
 * the resulting URL.
 *
 * Navigating to window.location.pathname keeps this working both at the
 * temporary /explore-next path and at /explore after cutover.
 *
 * @param {{circle: ?number, type: ?string, community: string, view: string}} filters
 */
export function useExploreNavigation(filters) {
    const visit = (patch) => {
        const next = { ...filters, ...patch };

        const query = {};
        if (next.circle) query.circle = next.circle;
        if (next.type) query.type = next.type;             // null = Locations (omit)
        if (next.community) query.community = next.community;
        if (next.view && next.view !== 'browse') query.view = next.view;

        router.get(window.location.pathname, query, {
            preserveState: true,
            preserveScroll: true,
        });
    };

    return {
        // Top-section type filter — changes WHAT is shown, never WHERE.
        selectType: (typeName) => visit({ type: typeName ?? null }),

        // Bottom-section type filter — independent of location and top type.
        selectCommunityType: (typeName) => visit({ community: typeName }),

        // Drill down into a location (child circle).
        selectCircle: (circleId) => visit({ circle: circleId }),

        // Jump to a breadcrumb crumb (null = national / South Africa).
        navigateToBreadcrumb: (circleId) => visit({ circle: circleId ?? null }),

        setViewMode: (mode) => visit({ view: mode }),
    };
}
