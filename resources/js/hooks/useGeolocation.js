import { useEffect, useState } from 'react';
import { getUserLocation } from '../utils/geolocation';

/**
 * On mount, request the browser's location and resolve it (via GET
 * /explore/nearest) to a "home community" suggestion { url, name } or null.
 * Any denial/failure leaves it null (the CTA stays hidden). Runs once.
 */
export function useGeolocation() {
    const [suggestion, setSuggestion] = useState(null);

    useEffect(() => {
        let active = true;

        getUserLocation()
            .then(({ latitude, longitude }) =>
                window.axios.get('/explore/nearest', { params: { lat: latitude, lng: longitude } }),
            )
            .then((response) => {
                if (active) {
                    setSuggestion(response.data.suggestion ?? null);
                }
            })
            .catch(() => {}); // permission denied / unavailable — stay hidden

        return () => {
            active = false;
        };
    }, []);

    return suggestion;
}
