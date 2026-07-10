import { useEffect } from 'react';

/**
 * Minimal headless dialog — the React replacement for wire-elements/modal.
 * Closes on Escape or backdrop click; locks body scroll while open.
 */
export default function Modal({ open, onClose, children }) {
    useEffect(() => {
        if (!open) {
            return;
        }

        const onKey = (e) => {
            if (e.key === 'Escape') {
                onClose();
            }
        };

        document.addEventListener('keydown', onKey);
        const prevOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';

        return () => {
            document.removeEventListener('keydown', onKey);
            document.body.style.overflow = prevOverflow;
        };
    }, [open, onClose]);

    if (!open) {
        return null;
    }

    return (
        <div
            className="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-black/50 p-4 sm:p-10"
            onMouseDown={onClose}
        >
            <div
                role="dialog"
                aria-modal="true"
                className="mt-10 w-full max-w-lg rounded-lg border border-border-muted bg-surface shadow-lg"
                onMouseDown={(e) => e.stopPropagation()}
            >
                {children}
            </div>
        </div>
    );
}
