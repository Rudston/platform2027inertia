import { useState } from 'react';

/**
 * Toggles the `.dark` class on <html> and persists the choice. The initial
 * class is set pre-paint by the inline script in app.blade.php (no FOUC).
 */
export default function DarkModeToggle() {
    const [dark, setDark] = useState(() => document.documentElement.classList.contains('dark'));

    const toggle = () => {
        const next = !dark;
        setDark(next);
        document.documentElement.classList.toggle('dark', next);
        localStorage.setItem('theme', next ? 'dark' : 'light');
    };

    return (
        <button
            type="button"
            onClick={toggle}
            className="rounded-lg border border-border-muted px-2 py-1.5 text-xs font-semibold transition hover:opacity-80"
            aria-label="Toggle dark mode"
        >
            {dark ? '☀️' : '🌙'}
        </button>
    );
}
