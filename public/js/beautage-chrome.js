(function () {
    'use strict';

    function toggleMobileNav() {
        const overlay = document.getElementById('mobile-nav-overlay');
        const drawer = document.getElementById('mobile-nav-drawer');
        if (!overlay || !drawer) {
            return;
        }
        const opening = drawer.classList.contains('-translate-x-full');
        overlay.classList.toggle('opacity-0', !opening);
        overlay.classList.toggle('pointer-events-none', !opening);
        drawer.classList.toggle('-translate-x-full', !opening);
    }

    function toggleMobileSearch() {
        const search = document.getElementById('mobile-search-container');
        if (!search) {
            return;
        }
        search.classList.toggle('hidden');
        if (!search.classList.contains('hidden')) {
            document.getElementById('mobile-search-input')?.focus();
        }
    }

    function toggleDrawer(drawerId, backdropId, panelId) {
        const drawer = document.getElementById(drawerId);
        const backdrop = document.getElementById(backdropId);
        const panel = document.getElementById(panelId);
        if (!drawer || !backdrop || !panel) {
            return;
        }
        if (drawer.classList.contains('hidden')) {
            drawer.classList.remove('hidden');
            window.setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('translate-x-full');
            }, 10);
            return;
        }
        backdrop.classList.add('opacity-0');
        panel.classList.add('translate-x-full');
        window.setTimeout(() => drawer.classList.add('hidden'), 300);
    }

    if (typeof window.toggleMobileNav !== 'function') {
        window.toggleMobileNav = toggleMobileNav;
    }
    if (typeof window.toggleMobileSearch !== 'function') {
        window.toggleMobileSearch = toggleMobileSearch;
    }
    if (typeof window.toggleCartDrawer !== 'function') {
        window.toggleCartDrawer = function () {
            toggleDrawer('cart-drawer', 'cart-backdrop', 'cart-panel');
        };
    }
    if (typeof window.toggleWishlistDrawer !== 'function') {
        window.toggleWishlistDrawer = function () {
            toggleDrawer('wishlist-drawer', 'wishlist-backdrop', 'wishlist-panel');
        };
    }
})();
