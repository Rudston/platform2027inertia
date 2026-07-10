import { useEffect, useRef, useState } from 'react';
import { Link } from '@inertiajs/react';
import { useTrans } from '../../hooks/useTrans';
import { useDebouncedSearch } from '../../hooks/useDebouncedSearch';

/**
 * Command-palette style search overlay. Debounced queries hit /explore/search;
 * results link to the community detail page (plain <a>, carrying ?from=).
 *
 * @param {{open: boolean, onClose: () => void, type: ?string, from: ?string}} props
 */
export default function SearchOverlay({ open, onClose, type = null, from = null }) {
    const t = useTrans();
    const [query, setQuery] = useState('');
    const inputRef = useRef(null);
    const { results } = useDebouncedSearch(query, { type, from });

    // Focus on open; reset query on close; close on Escape.
    useEffect(() => {
        if (open) {
            inputRef.current?.focus();
        } else {
            setQuery('');
        }
    }, [open]);

    useEffect(() => {
        if (!open) {
            return;
        }
        const onKey = (e) => e.key === 'Escape' && onClose();
        window.addEventListener('keydown', onKey);
        return () => window.removeEventListener('keydown', onKey);
    }, [open, onClose]);

    if (!open) {
        return null;
    }

    const tooShort = query.trim().length < 2;

    return (
        <div
            className="fixed inset-0 z-50 bg-black/40 p-4"
            onMouseDown={(e) => e.target === e.currentTarget && onClose()}
        >
            <div className="mx-auto mt-16 max-w-2xl overflow-hidden rounded-xl bg-surface shadow-2xl">
                <div className="flex items-center gap-2 border-b border-border-muted px-4 py-3">
                    <span aria-hidden="true">🔍</span>
                    <input
                        ref={inputRef}
                        type="text"
                        value={query}
                        onChange={(e) => setQuery(e.target.value)}
                        placeholder={t('explore.search_placeholder')}
                        className="w-full border-0 bg-transparent p-0 text-main placeholder-muted focus:ring-0"
                    />
                    <button
                        type="button"
                        onClick={onClose}
                        className="text-muted transition hover:text-main"
                        aria-label={t('explore.close_search')}
                    >
                        ✕
                    </button>
                </div>

                <div className="max-h-96 overflow-y-auto">
                    {results.length > 0 ? (
                        results.map((result) => (
                            <Link
                                key={result.id}
                                href={result.url}
                                onClick={onClose}
                                className="flex w-full items-center justify-between gap-3 px-4 py-3 text-left transition hover:bg-border-muted"
                            >
                                <span className="min-w-0">
                                    <span className="block truncate text-main">{result.name}</span>
                                    <span className="block truncate text-xs text-muted">
                                        📍 {result.location ?? '—'}
                                    </span>
                                </span>
                                <span className="shrink-0 rounded-full bg-border-muted px-2 py-0.5 text-xs font-medium text-muted">
                                    {result.badge}
                                </span>
                            </Link>
                        ))
                    ) : (
                        <div className="px-4 py-8 text-center text-sm text-muted">
                            {tooShort
                                ? t('explore.search_min_chars')
                                : t('explore.search_no_results', { query })}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
