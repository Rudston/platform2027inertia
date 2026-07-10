/**
 * Horizontal pill bar for a filter group. `pills` come from the server
 * (filterBars.location / filterBars.community); `active` is the current
 * selection value (enum short name, or null for Locations).
 *
 * @param {{pills: Array, active: ?string, onSelect: (value: ?string) => void}} props
 */
export default function TypeFilterBar({ pills, active, onSelect }) {
    return (
        <div className="flex gap-2 overflow-x-auto py-3">
            {pills.map((pill) => {
                const isActive = active === pill.value;

                return (
                    <button
                        key={pill.value ?? 'locations'}
                        type="button"
                        onClick={() => onSelect(pill.value)}
                        className={[
                            'flex shrink-0 items-center gap-1.5 rounded-full border px-4 py-2 text-sm font-medium transition',
                            isActive
                                ? 'border-indigo-600 bg-indigo-600 text-white shadow-sm'
                                : 'border-border-muted bg-surface text-muted hover:bg-border-muted',
                        ].join(' ')}
                    >
                        <span aria-hidden="true">{pill.icon}</span>
                        <span>{pill.label}</span>
                    </button>
                );
            })}
        </div>
    );
}
