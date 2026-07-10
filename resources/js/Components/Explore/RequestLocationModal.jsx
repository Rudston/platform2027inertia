import { useState } from 'react';
import Modal from '../Modal';
import { useTrans } from '../../hooks/useTrans';

/**
 * "Request an unlisted location" modal. Placeholder — no save yet (mirrors the
 * Livewire RequestLocationModal); submitting just closes.
 *
 * @param {{open: boolean, onClose: () => void, parentLocationName: string}} props
 */
export default function RequestLocationModal({ open, onClose, parentLocationName = '' }) {
    const t = useTrans();
    const [locationName, setLocationName] = useState('');

    const submit = () => {
        // TODO: implement save/notification logic
        setLocationName('');
        onClose();
    };

    return (
        <Modal open={open} onClose={onClose}>
            <div className="p-6">
                <div className="flex items-start justify-between gap-4">
                    <h2 className="text-xl font-bold text-main">
                        {t('communities.request_modal.title', { place: parentLocationName })}
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

                <p className="mt-2 text-sm text-muted">{t('communities.request_modal.subtitle')}</p>

                <div className="mt-5">
                    <label htmlFor="request-location-name" className="block text-sm font-medium text-main">
                        {t('communities.request_modal.location_name')}
                    </label>
                    <input
                        type="text"
                        id="request-location-name"
                        value={locationName}
                        onChange={(e) => setLocationName(e.target.value)}
                        className="mt-1 block w-full rounded-md border border-border-muted bg-surface px-3 py-2 text-sm text-main shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                    />
                </div>

                <div className="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        onClick={onClose}
                        className="rounded-lg border border-border-muted px-4 py-2 text-sm font-medium text-muted transition hover:bg-border-muted"
                    >
                        {t('ui.cancel')}
                    </button>
                    <button
                        type="button"
                        onClick={submit}
                        className="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700"
                    >
                        {t('communities.request_modal.send')}
                    </button>
                </div>
            </div>
        </Modal>
    );
}
