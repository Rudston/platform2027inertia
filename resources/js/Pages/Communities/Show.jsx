import { Head, Link } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import { useTrans } from '../../hooks/useTrans';

/**
 * Community (circle) detail page — the React counterpart of the Livewire
 * CommunityPage. `backUrl` restores the exact Explore view we came from
 * (validated server-side to an internal /explore path). Join is a placeholder,
 * as in the Livewire version.
 */
export default function Show({ circle, administrators, backUrl }) {
    const t = useTrans();

    return (
        <>
            <Head title={circle.name} />

            <div className="mx-auto min-h-screen w-4/5 py-10">
                <Link href={backUrl} className="text-sm text-indigo-600 hover:underline">
                    {t('communities.page.back')}
                </Link>

                <div className="mt-4 rounded-lg border border-border-muted bg-surface p-8 shadow-sm">
                    {/* Header: type icon + name */}
                    <div className="flex items-start gap-3">
                        <span className="text-3xl" aria-hidden="true">
                            {circle.communityType?.icon ?? '🌍'}
                        </span>
                        <h1 className="text-2xl font-bold text-main">{circle.name}</h1>
                    </div>

                    {/* Geographic line */}
                    <div className="mt-3 flex items-center gap-1.5 text-sm text-muted">
                        <span aria-hidden="true">📍</span>
                        <span>{circle.locationName ?? '—'}</span>
                    </div>

                    {/* Administrators */}
                    <div className="mt-2 flex items-center gap-1.5 text-sm text-muted">
                        <span aria-hidden="true">🛡️</span>
                        <span className="font-medium text-main">{t('communities.page.admins')}:</span>
                        <span>
                            {administrators.length > 0
                                ? administrators.join(', ')
                                : t('communities.page.no_admins')}
                        </span>
                    </div>

                    {/* Description */}
                    {circle.description && <p className="mt-4 text-muted">{circle.description}</p>}

                    {/* Active services */}
                    {circle.services.length > 0 && (
                        <div className="mt-6">
                            <h2 className="text-xs font-semibold uppercase tracking-wide text-muted">
                                {t('communities.page.services')}
                            </h2>
                            <div className="mt-2 flex flex-wrap gap-2">
                                {circle.services.map((service) => (
                                    <span
                                        key={service}
                                        className="rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700"
                                    >
                                        ⚙️ {service}
                                    </span>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Member count placeholder */}
                    <div className="mt-6 text-sm text-muted">
                        {t('communities.page.members', { count: circle.memberCount })}
                    </div>

                    {/* Join (placeholder — membership system is separate) */}
                    <div className="mt-6">
                        <button
                            type="button"
                            onClick={() => {}}
                            className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-indigo-700"
                        >
                            {t('communities.page.join')}
                        </button>
                    </div>
                </div>
            </div>
        </>
    );
}

Show.layout = (page) => <AppLayout>{page}</AppLayout>;
