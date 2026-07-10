import { useTrans } from '../../hooks/useTrans';

/**
 * Empty-state panel. Two CTA modes:
 *   - addLabel + onAdd  → "+ Add {label}" (opens the Add-community modal)
 *   - ctaLabel + onCta  → legacy "start a …" button (top location browser)
 * Optionally shows a "N in sub-regions" footer.
 *
 * @param {{
 *   icon: string, heading: string, subheading?: string,
 *   addLabel?: ?string, onAdd?: ?() => void,
 *   ctaLabel?: ?string, onCta?: ?() => void,
 *   belowCount?: number, belowLabel?: string,
 * }} props
 */
export default function EmptyState({
    icon,
    heading,
    subheading = '',
    addLabel = null,
    onAdd = null,
    ctaLabel = null,
    onCta = null,
    belowCount = 0,
    belowLabel = '',
}) {
    const t = useTrans();

    return (
        <div className="rounded-lg border border-dashed border-border-muted bg-surface p-10 text-center">
            <div className="text-5xl" aria-hidden="true">{icon}</div>

            <h2 className="mt-4 text-lg font-semibold text-main">{heading}</h2>
            {subheading && <p className="mt-1 text-sm text-muted">{subheading}</p>}

            {addLabel ? (
                <button
                    type="button"
                    onClick={onAdd}
                    className="mt-5 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700"
                >
                    {t('explore.add_community', { label: addLabel })}
                </button>
            ) : ctaLabel ? (
                <button
                    type="button"
                    onClick={onCta}
                    className="mt-5 inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700"
                >
                    {ctaLabel}
                </button>
            ) : null}

            {belowCount > 0 && (
                <div className="mx-auto mt-8 max-w-xs border-t border-border-muted pt-4 text-sm text-muted">
                    <span className="font-semibold text-main">{belowCount}</span> {belowLabel}
                    <div className="mt-0.5 text-muted">{t('explore.empty.in_sub_regions')}</div>
                </div>
            )}
        </div>
    );
}
