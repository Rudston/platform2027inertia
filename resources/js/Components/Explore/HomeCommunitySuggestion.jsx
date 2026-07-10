import { useTrans } from '../../hooks/useTrans';

/**
 * "Your home community" CTA, shown once geolocation resolves to a nearest
 * MainPlace circle. Plain <a> (full navigation) — transition-safe.
 *
 * @param {{suggestion: ?{url: string, name: string}}} props
 */
export default function HomeCommunitySuggestion({ suggestion }) {
    const t = useTrans();

    if (!suggestion) {
        return null;
    }

    return (
        <div className="mb-4 flex justify-end">
            <a
                href={suggestion.url}
                className="inline-flex items-center gap-1.5 rounded-lg border border-indigo-600 bg-surface px-3 py-2 text-sm font-medium text-indigo-600 shadow-sm transition hover:bg-indigo-50"
            >
                {t('explore.home_community')}
            </a>
        </div>
    );
}
