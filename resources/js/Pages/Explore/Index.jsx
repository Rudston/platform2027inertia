import { useState } from 'react';
import { Head } from '@inertiajs/react';
import AppLayout from '../../Layouts/AppLayout';
import { useTrans } from '../../hooks/useTrans';
import { useExploreNavigation } from '../../hooks/useExploreNavigation';
import { useGeolocation } from '../../hooks/useGeolocation';
import TypeFilterBar from '../../Components/Explore/TypeFilterBar';
import Breadcrumb from '../../Components/Explore/Breadcrumb';
import ViewModeToggle from '../../Components/Explore/ViewModeToggle';
import LocationColumn from '../../Components/Explore/LocationColumn';
import CommunityGrid from '../../Components/Explore/CommunityGrid';
import CommunityCard from '../../Components/Explore/CommunityCard';
import EmptyState from '../../Components/Explore/EmptyState';
import HomeCommunitySuggestion from '../../Components/Explore/HomeCommunitySuggestion';
import SearchOverlay from '../../Components/Explore/SearchOverlay';
import AddCommunityModal from '../../Components/Explore/AddCommunityModal';
import RequestLocationModal from '../../Components/Explore/RequestLocationModal';

const LOCATIONS_COLUMN_MAX_HEIGHT = '400px';

export default function Index({
    filters,
    exploreUrl,
    breadcrumb,
    currentLevel,
    isAtTerminalLevel,
    location,
    rightColumnCircle,
    typeSection,
    meta,
    filterBars,
}) {
    const t = useTrans();
    const nav = useExploreNavigation(filters);
    const suggestion = useGeolocation();

    const [searchOpen, setSearchOpen] = useState(false);
    const [addModal, setAddModal] = useState({ open: false, label: '' });
    const [requestModal, setRequestModal] = useState({ open: false, parentLocationName: '' });

    const placeName = breadcrumb[breadcrumb.length - 1]?.name ?? 'South Africa';
    // Non-location top type appends a plural label to the breadcrumb trail.
    const topTypeLabel = filters.type && filters.type !== 'LocationCommunity' ? meta.top.label : null;

    const openAddModal = () => setAddModal({ open: true, label: typeSection.addLabel });

    const renderLocationBrowser = () => {
        if (filters.view !== 'browse') {
            return (
                <div className="rounded-lg border border-dashed border-border-muted bg-surface p-10 text-center text-muted">
                    {t('explore.map_coming_soon')}
                </div>
            );
        }

        if (location.communities.length > 0) {
            return (
                <LocationColumn
                    communities={location.communities}
                    heading={placeName}
                    onSelectCircle={nav.selectCircle}
                    onRequestLocation={() =>
                        setRequestModal({ open: true, parentLocationName: placeName })
                    }
                />
            );
        }

        if (isAtTerminalLevel) {
            return (
                <div className="rounded-lg border border-border-muted bg-surface px-4 py-6 text-center text-sm text-muted">
                    {t('explore.no_further_subareas')}
                </div>
            );
        }

        // Empty at this level — offer to start one, noting any below.
        return (
            <EmptyState
                icon={meta.top.icon}
                heading={
                    location.countBelow > 0
                        ? t('explore.empty.none_at_level', { type: meta.top.label, level: currentLevel })
                        : t('explore.empty.none_here', { type: meta.top.label })
                }
                subheading={
                    location.countBelow > 0
                        ? t('explore.empty.be_first_text')
                        : t('explore.empty.fresh_text')
                }
                ctaLabel={
                    location.countBelow > 0
                        ? t('explore.empty.start_a', { type: meta.top.singular })
                        : t('explore.empty.be_first_cta')
                }
                onCta={() => {}} /* start-a-community flow wired later */
                belowCount={location.countBelow}
                belowLabel={meta.top.label}
            />
        );
    };

    const renderTypeSection = () => {
        if (typeSection.communities.length > 0) {
            return (
                <>
                    <div className="-mt-4 mb-4 flex justify-end">
                        <button
                            type="button"
                            onClick={openAddModal}
                            className="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700"
                        >
                            {t('explore.add_community', { label: typeSection.addLabel })}
                        </button>
                    </div>
                    <CommunityGrid communities={typeSection.communities} />
                </>
            );
        }

        return (
            <EmptyState
                icon={meta.bottom.icon}
                heading={
                    typeSection.countBelow > 0
                        ? t('explore.empty.none_at_level', { type: meta.bottom.label, level: currentLevel })
                        : t('explore.empty.none_here', { type: meta.bottom.label })
                }
                subheading={
                    typeSection.countBelow > 0
                        ? t('explore.empty.be_first_text')
                        : t('explore.empty.fresh_text')
                }
                addLabel={typeSection.addLabel}
                onAdd={openAddModal}
                belowCount={typeSection.countBelow}
                belowLabel={meta.bottom.label}
            />
        );
    };

    return (
        <>
            <Head title={t('explore.title')} />

            <div className="mx-auto max-w-5xl px-4 py-6">
                {/* ===== TOP SECTION — location explorer + selected-location card ===== */}
                <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    {/* LEFT — geographic drill-down */}
                    <div>
                        <div className="flex items-center justify-between gap-4">
                            <h1 className="text-xl font-bold tracking-tight text-main">
                                {t('explore.title')}
                            </h1>
                            <button
                                type="button"
                                onClick={() => setSearchOpen(true)}
                                className="inline-flex items-center gap-1.5 rounded-lg border border-border-muted bg-surface px-3 py-2 text-sm font-medium text-muted shadow-sm transition hover:bg-border-muted"
                            >
                                <span aria-hidden="true">🔍</span> {t('ui.search')}
                            </button>
                        </div>

                        {/* TODO(content-blocks): admin-editable "explore.welcome_banner"
                            was a Livewire <x-content-block>; expose as a prop later. */}

                        <div className="mt-2">
                            <TypeFilterBar
                                pills={filterBars.location}
                                active={filters.type}
                                onSelect={nav.selectType}
                            />
                        </div>

                        <div className="mt-1 flex flex-wrap items-center justify-between gap-2">
                            <Breadcrumb
                                trail={breadcrumb}
                                typeLabel={topTypeLabel}
                                onNavigate={nav.navigateToBreadcrumb}
                            />
                            <ViewModeToggle view={filters.view} onChange={nav.setViewMode} />
                        </div>

                        <div
                            className="mt-4 overflow-y-auto"
                            style={{ maxHeight: LOCATIONS_COLUMN_MAX_HEIGHT }}
                        >
                            {renderLocationBrowser()}
                        </div>
                    </div>

                    {/* RIGHT — selected-location card (defaults to national) */}
                    <div className="flex flex-col">
                        <HomeCommunitySuggestion suggestion={suggestion} />

                        {rightColumnCircle ? (
                            <div className="mt-auto">
                                <CommunityCard circle={rightColumnCircle} />
                            </div>
                        ) : (
                            <div className="mt-auto flex min-h-40 items-center justify-center rounded-lg border border-dashed border-border-muted bg-surface p-10 text-center text-sm text-muted">
                                {t('explore.select_location')}
                            </div>
                        )}
                    </div>
                </div>

                {/* ===== BOTTOM SECTION — community types at the selected location ===== */}
                <div className="mt-10 border-t border-border-muted pt-8">
                    <h2 className="text-lg font-semibold tracking-tight text-main">
                        {t('explore.communities_in', { place: placeName })}
                    </h2>
                    <p className="mt-0.5 text-sm text-muted">{t('explore.bottom_subtext')}</p>

                    <div className="mt-2">
                        <TypeFilterBar
                            pills={filterBars.community}
                            active={filters.community}
                            onSelect={nav.selectCommunityType}
                        />
                    </div>

                    <div className="mt-4">{renderTypeSection()}</div>
                </div>
            </div>

            <SearchOverlay
                open={searchOpen}
                onClose={() => setSearchOpen(false)}
                type={filters.type}
                from={exploreUrl}
            />

            <AddCommunityModal
                open={addModal.open}
                onClose={() => setAddModal({ open: false, label: '' })}
                label={addModal.label}
            />

            <RequestLocationModal
                open={requestModal.open}
                onClose={() => setRequestModal({ open: false, parentLocationName: '' })}
                parentLocationName={requestModal.parentLocationName}
            />
        </>
    );
}

Index.layout = (page) => <AppLayout>{page}</AppLayout>;
