function beautageRunWhenIdle(callback) {
    if (typeof window.requestIdleCallback === 'function') {
        window.requestIdleCallback(callback, { timeout: 2200 });
        return;
    }
    window.setTimeout(callback, 1);
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.BeautageCart) {
        window.BeautageCart.syncPageCartVariable();
        window.BeautageCart.updateCartUI();
    }

    beautageRunWhenIdle(() => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
});
