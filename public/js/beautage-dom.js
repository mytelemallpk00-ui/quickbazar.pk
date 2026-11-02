(function () {
    const PLACEHOLDER_PRODUCT = 'https://placehold.co/400x400/5D267D/FFFFFF?text=Beautage';
    const PLACEHOLDER_THUMB = 'https://placehold.co/100x100/5D267D/FFFFFF?text=Item';

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function escapeAttr(value) {
        return escapeHtml(value).replace(/`/g, '&#96;');
    }

    function parseJsonResponse(response) {
        return response.text().then((text) => {
            if (!text) {
                return {};
            }
            try {
                return JSON.parse(text);
            } catch (e) {
                return { success: false, message: 'Invalid server response.' };
            }
        });
    }

    function whenIdle(callback) {
        if (typeof window.requestIdleCallback === 'function') {
            window.requestIdleCallback(callback, { timeout: 1500 });
            return;
        }
        window.setTimeout(callback, 1);
    }

    window.BeautageDom = {
        escapeHtml,
        escapeAttr,
        parseJsonResponse,
        whenIdle,
        PLACEHOLDER_PRODUCT,
        PLACEHOLDER_THUMB,
    };
})();
