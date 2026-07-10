import { useTrans } from '../../hooks/useTrans';

/**
 * Browse / Map toggle. Map is disabled (Phase 1 — "coming soon"), matching the
 * Livewire UI.
 *
 * @param {{view: string, onChange: (mode: string) => void}} props
 */
export default function ViewModeToggle({ view, onChange }) {
    const t = useTrans();

    return (
        <div className="flex items-center gap-1 rounded-lg border border-border-muted bg-surface p-0.5 shadow-sm">
            <button
                type="button"
                disabled
                title="Coming soon"
                className="inline-flex cursor-not-allowed items-center gap-1 rounded-md px-3 py-1.5 text-sm font-medium text-muted opacity-60"
            >
                <span aria-hidden="true">🗺</span> {t('explore.view_mode.map')}
            </button>
            <button
                type="button"
                onClick={() => onChange('browse')}
                className={[
                    'inline-flex items-center gap-1 rounded-md px-3 py-1.5 text-sm font-medium transition',
                    view === 'browse' ? 'bg-indigo-600 text-white' : 'text-muted hover:bg-border-muted',
                ].join(' ')}
            >
                <span aria-hidden="true">☰</span> {t('explore.view_mode.browse')}
            </button>
        </div>
    );
}
