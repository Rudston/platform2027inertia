import CommunityCard from './CommunityCard';

/**
 * Responsive grid of community cards (non-location types).
 *
 * @param {{communities: Array}} props
 */
export default function CommunityGrid({ communities }) {
    return (
        <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {communities.map((circle) => (
                <CommunityCard key={circle.id} circle={circle} />
            ))}
        </div>
    );
}
