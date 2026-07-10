import { Link, usePage } from '@inertiajs/react';
import { useTrans } from '../hooks/useTrans';
import DarkModeToggle from '../Components/DarkModeToggle';

/**
 * Public app shell (nav + theme toggle) — the React counterpart of the old
 * layouts/main.blade.php. Persistent layout, assigned via `Page.layout` so it
 * isn't remounted between Explore visits.
 */
export default function AppLayout({ children }) {
    const t = useTrans();
    const { appName } = usePage().props;

    return (
        <div className="min-h-screen bg-surface text-main antialiased">
            <nav className="border-b border-border-muted bg-surface">
                <div className="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 text-sm">
                    <div className="flex items-center gap-6">
                        <Link href="/explore" className="font-semibold">{appName}</Link>
                        <Link href="/explore" className="hover:underline">
                            {t('navigation.explore_communities')}
                        </Link>
                    </div>
                    <div className="flex items-center gap-3">
                        <DarkModeToggle />
                    </div>
                </div>
            </nav>

            <main>{children}</main>
        </div>
    );
}
