import { usePage } from '@inertiajs/react';

/**
 * Translate a key from the server-shared translation bag (see
 * HandleInertiaRequests). Supports Laravel-style :placeholder replacement.
 *
 *   const t = useTrans();
 *   t('explore.title');
 *   t('explore.communities_in', { place: 'Gauteng' });
 */
export function useTrans() {
    const { translations } = usePage().props;

    return (key, replacements = {}) => {
        let value = translations?.[key] ?? key;

        for (const [token, replacement] of Object.entries(replacements)) {
            value = value.split(`:${token}`).join(String(replacement));
        }

        return value;
    };
}
