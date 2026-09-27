/**
 * Modern Custom Alert & Confirm System using SweetAlert2
 * Beautiful notifications, form confirm helpers & backward-compatible window.alert override.
 */

(function () {
    // Store reference to native window.alert & confirm
    const originalAlert = window.alert;
    const originalConfirm = window.confirm;

    function parseAlertParams(message) {
        let text = message !== undefined && message !== null ? String(message) : '';
        let icon = 'info';
        let title = 'Notification';

        if (text.startsWith('✅')) {
            icon = 'success';
            text = text.replace(/^✅\s*/, '');
            title = 'Success';
        } else if (text.startsWith('❌')) {
            icon = 'error';
            text = text.replace(/^❌\s*/, '');
            title = 'Error';
        } else if (text.startsWith('⚠️')) {
            icon = 'warning';
            text = text.replace(/^⚠️\s*/, '');
            title = 'Warning';
        } else {
            const lower = text.toLowerCase();
            if (lower.includes('error') || lower.includes('failed') || lower.includes('সমস্যা') || lower.includes('ব্যর্থ') || lower.includes('ভুল')) {
                icon = 'error';
                title = 'Error';
            } else if (lower.includes('success') || lower.includes('saved') || lower.includes('সফল') || lower.includes('সেভ')) {
                icon = 'success';
                title = 'Success';
            } else if (lower.includes('warning') || lower.includes('please') || lower.includes('অনুগ্রহ করে') || lower.includes('দিন') || lower.includes('নির্বাচন করুন')) {
                icon = 'warning';
                title = 'Notice';
            }
        }

        return { text, icon, title };
    }

    // Modern Toast Helper
    window.showToast = function (message, icon = null, timer = 3500) {
        if (typeof Swal === 'undefined') {
            originalAlert(message);
            return Promise.resolve();
        }

        const parsed = parseAlertParams(message);
        const finalIcon = icon || parsed.icon;

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: timer,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        return Toast.fire({
            icon: finalIcon,
            title: parsed.text || message
        });
    };

    // Modern Alert Popup Helper
    window.showAlert = function (title, message, icon = null) {
        if (typeof Swal === 'undefined') {
            originalAlert((title ? title + ': ' : '') + (message || ''));
            return Promise.resolve();
        }

        const parsed = parseAlertParams(message || title);
        const finalTitle = title || parsed.title;
        const finalText = message !== undefined ? parsed.text : (parsed.text || title);
        const finalIcon = icon || parsed.icon;

        return Swal.fire({
            title: finalTitle,
            text: finalText,
            icon: finalIcon,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'OK',
            buttonsStyling: true
        });
    };

    // Modern Confirm Dialog Helper
    window.showConfirm = function (title, text, onConfirm, onCancel, options = {}) {
        if (typeof Swal === 'undefined') {
            if (originalConfirm(`${title}\n${text}`)) {
                if (onConfirm) onConfirm();
            } else {
                if (onCancel) onCancel();
            }
            return Promise.resolve();
        }

        return Swal.fire({
            title: title || 'Are you sure?',
            text: text || '',
            icon: options.icon || 'warning',
            showCancelButton: true,
            confirmButtonColor: options.confirmButtonColor || '#dc2626',
            cancelButtonColor: options.cancelButtonColor || '#4b5563',
            confirmButtonText: options.confirmButtonText || 'Yes, proceed',
            cancelButtonText: options.cancelButtonText || 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                if (onConfirm) onConfirm(result);
            } else if (result.isDismissed) {
                if (onCancel) onCancel(result);
            }
        });
    };

    // Helper for Form Delete Confirmation (used in onsubmit)
    window.confirmDelete = function (event, title = 'Are you sure?', text = 'This action cannot be undone.') {
        event.preventDefault();
        const target = event.currentTarget || event.target;
        const form = target.tagName === 'FORM' ? target : target.closest('form');

        if (typeof Swal === 'undefined') {
            if (originalConfirm(title)) {
                if (form) form.submit();
            }
            return false;
        }

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed && form) {
                form.submit();
            }
        });

        return false;
    };

    // Helper for Link/Action Confirmations (used in onclick)
    window.confirmAction = function (event, title = 'Are you sure?', text = '') {
        event.preventDefault();
        const target = event.currentTarget || event.target;
        const href = target.getAttribute('href');

        if (typeof Swal === 'undefined') {
            if (originalConfirm(title)) {
                if (href) window.location.href = href;
            }
            return false;
        }

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#4b5563',
            confirmButtonText: 'Yes, proceed',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed && href && href !== '#') {
                window.location.href = href;
            }
        });

        return false;
    };

    // Global alert override
    window.alert = function (message) {
        if (typeof Swal === 'undefined') {
            originalAlert(message);
            return;
        }

        const parsed = parseAlertParams(message);
        const textStr = String(parsed.text || message || '');

        if (textStr.length < 120) {
            window.showToast(message, parsed.icon);
        } else {
            window.showAlert(parsed.title, message, parsed.icon);
        }
    };
})();
