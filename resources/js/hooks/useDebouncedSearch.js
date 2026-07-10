import { useEffect, useState } from 'react';

/**
 * Debounced autocomplete against GET /explore/search. Returns { results,
 * loading }. Queries under 2 characters resolve to an empty list without a
 * request (mirrors the server's own guard).
 *
 * @param {string} query
 * @param {{type: ?string, from: ?string, delay?: number}} options
 */
export function useDebouncedSearch(query, { type = null, from = null, delay = 250 } = {}) {
    const [results, setResults] = useState([]);
    const [loading, setLoading] = useState(false);

    useEffect(() => {
        const q = query.trim();

        if (q.length < 2) {
            setResults([]);
            setLoading(false);
            return;
        }

        setLoading(true);
        const controller = new AbortController();

        const timer = setTimeout(() => {
            window.axios
                .get('/explore/search', { params: { q, type, from }, signal: controller.signal })
                .then((response) => setResults(response.data.results ?? []))
                .catch(() => {}) // aborted or failed — leave prior results
                .finally(() => setLoading(false));
        }, delay);

        return () => {
            clearTimeout(timer);
            controller.abort();
        };
    }, [query, type, from, delay]);

    return { results, loading };
}
