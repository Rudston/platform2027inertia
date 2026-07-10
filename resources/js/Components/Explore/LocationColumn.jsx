import { useTrans } from '../../hooks/useTrans';

/**
 * File-browser style column for the location drill-down. Rows navigate deeper
 * via onSelectCircle(id). At the terminal geographic level a "request a
 * location" action is offered (gated later).
 *
 * @param {{
 *   communities: Array,
 *   heading: ?string,
 *   onSelectCircle: (id: number) => void,
 *   onRequestLocation: ?() => void,
 * }} props
 */
export default function LocationColumn({ communities, heading, onSelectCircle, onRequestLocation }) {
    const t = useTrans();

    // Terminal when the first row is at the bottom geographic level.
    const isTerminal = communities.length > 0 && (communities[0].level?.isTerminal ?? false);

    return (
        <div className="overflow-hidden rounded-lg border border-border-muted bg-surface shadow-sm">
            {heading && (
                <div className="border-b border-border-muted px-4 py-3">
                    <h2 className="font-semibold text-main">{heading}</h2>
                </div>
            )}

            <ul className="divide-y divide-border-muted">
                {communities.map((circle) => (
                    <li key={circle.id}>
                        <button
                            type="button"
                            onClick={() => onSelectCircle(circle.id)}
                            className="flex w-full items-center justify-between gap-3 px-4 py-3 text-left transition hover:bg-border-muted"
                        >
                            <span className="flex min-w-0 items-center gap-2">
                                <span className="text-muted" aria-hidden="true">▸</span>
                                <span className="truncate text-main">{circle.displayName}</span>
                                {circle.alsoHere && (
                                    <span className="shrink-0 rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-600">
                                        {t('explore.also_here')}
                                    </span>
                                )}
                            </span>
                            {circle.level && (
                                <span className="shrink-0 rounded-full bg-border-muted px-2 py-0.5 text-xs font-medium text-muted">
                                    {circle.level.badgeList}
                                </span>
                            )}
                        </button>
                    </li>
                ))}
            </ul>

            {isTerminal && onRequestLocation && (
                <div className="border-t border-border-muted px-4 py-3 text-center">
                    <button
                        type="button"
                        onClick={onRequestLocation}
                        className="text-sm text-indigo-600 hover:underline"
                    >
                        {t('explore.request_location')}
                    </button>
                </div>
            )}
        </div>
    );
}
