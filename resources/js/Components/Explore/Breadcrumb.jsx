/**
 * Geographic breadcrumb. Each crumb navigates via onNavigate(id) (null = South
 * Africa). When a plural typeLabel is present (a non-location filter), it is
 * appended as the current segment and the last location crumb becomes clickable.
 *
 * @param {{trail: Array<{id: ?number, name: string}>, typeLabel: ?string, onNavigate: (id: ?number) => void}} props
 */
export default function Breadcrumb({ trail, typeLabel = null, onNavigate }) {
    return (
        <nav className="flex flex-wrap items-center gap-1.5 py-2 text-sm" aria-label="Breadcrumb">
            <span aria-hidden="true">📍</span>

            {trail.map((crumb, index) => {
                const isLast = index === trail.length - 1;
                const isCurrent = isLast && !typeLabel;

                return (
                    <span key={crumb.id ?? 'national'} className="flex items-center gap-1.5">
                        {isCurrent ? (
                            <span className="font-semibold text-main">{crumb.name}</span>
                        ) : (
                            <button
                                type="button"
                                onClick={() => onNavigate(crumb.id)}
                                className="text-indigo-600 hover:underline"
                            >
                                {crumb.name}
                            </button>
                        )}

                        {(!isLast || typeLabel) && (
                            <span className="text-muted" aria-hidden="true">›</span>
                        )}
                    </span>
                );
            })}

            {typeLabel && <span className="font-semibold text-main">{typeLabel}</span>}
        </nav>
    );
}
