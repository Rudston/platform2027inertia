import { Link } from '@inertiajs/react';
import { useTrans } from '../../hooks/useTrans';

/**
 * Card for a non-location community (Organisation / Campaign / Course / Event /
 * Theme). `circle` is a CircleSummaryResource; `circle.url` is the detail-page
 * link (server-provided, already carrying ?from=).
 *
 * @param {{circle: object}} props
 */
export default function CommunityCard({ circle }) {
    const t = useTrans();
    const isPending = circle.status === 'pending';

    return (
        <div className="flex flex-col rounded-lg border border-border-muted bg-surface p-4 shadow-sm">
            <div className="flex items-start justify-between">
                <span className="text-2xl" aria-hidden="true">{circle.communityType?.icon ?? '🌍'}</span>
                {circle.level && (
                    <span className="shrink-0 rounded-full bg-border-muted px-2 py-0.5 text-xs font-medium text-muted">
                        {circle.level.badgeCard}
                    </span>
                )}
            </div>

            <h3 className="mt-2 font-semibold text-main">{circle.name}</h3>
            {circle.description && (
                <p className="mt-1 line-clamp-2 text-sm text-muted">{circle.description}</p>
            )}

            <div className="mt-4 flex items-center justify-between">
                <span className="text-xs text-muted">{t('communities.card.members', { count: 0 })}</span>
                <div className="flex items-center gap-2">
                    {isPending && (
                        <span className="shrink-0 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">
                            {t('communities.status_pending')}
                        </span>
                    )}
                    <Link
                        href={circle.url}
                        className="rounded-lg border border-indigo-600 px-3 py-1.5 text-sm font-medium text-indigo-600 transition hover:bg-indigo-50"
                    >
                        {t('ui.view')}
                    </Link>
                </div>
            </div>
        </div>
    );
}
