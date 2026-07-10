import Modal from '../Modal';
import { useTrans } from '../../hooks/useTrans';

/**
 * Add-community modal. The write-flow requires an authenticated requester,
 * which this standalone build has no in-app auth for — so it shows a neutral
 * "account required" notice (gated; the host app re-enables it). See CLAUDE.md.
 *
 * @param {{open: boolean, onClose: () => void, label: ?string}} props
 */
export default function AddCommunityModal({ open, onClose, label = '' }) {
    const t = useTrans();

    return (
        <Modal open={open} onClose={onClose}>
            <div className="p-6">
                <div className="flex items-start justify-between gap-4">
                    <h2 className="text-xl font-bold text-main">
                        {t('communities.add_modal.title', { label })}
                    </h2>
                    <button
                        type="button"
                        onClick={onClose}
                        className="text-muted transition hover:text-main"
                        aria-label={t('ui.close')}
                    >
                        ✕
                    </button>
                </div>

                <div className="p-6 text-center text-sm text-muted">
                    {t('communities.add_requires_account')}
                </div>
            </div>
        </Modal>
    );
}
