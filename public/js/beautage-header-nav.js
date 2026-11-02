(function () {
    'use strict';

    const STRIP_ID = 'storefront-category-strip';
    const LG = 1024;

    function isDesktop() {
        return window.matchMedia(`(min-width: ${LG}px)`).matches;
    }

    function getPanel(root) {
        return root.querySelector('.collections-mega-panel, .nav-dropdown-panel');
    }

    function closeAll() {
        document.body.classList.remove('storefront-nav-open');
        document.querySelectorAll('[data-nav-dropdown]').forEach((root) => {
            const panel = getPanel(root);
            if (panel) {
                panel.classList.remove('is-open');
                panel.style.top = '';
            }
            const btn = root.querySelector('[data-nav-dropdown-toggle]');
            if (btn) {
                btn.setAttribute('aria-expanded', 'false');
            }
        });
        document.getElementById(STRIP_ID)?.classList.remove('mega-open');
        window.BeautageHeaderNav.openId = null;
    }

    function positionMobilePanel(root, panel) {
        if (isDesktop()) {
            panel.style.top = '';

            return;
        }
        const btn = root.querySelector('[data-nav-dropdown-toggle]');
        if (!btn) {
            return;
        }
        const rect = btn.getBoundingClientRect();
        panel.style.top = `${Math.round(rect.bottom + 8)}px`;
    }

    function toggle(root) {
        if (!root) {
            return;
        }

        const panel = getPanel(root);
        const btn = root.querySelector('[data-nav-dropdown-toggle]');
        if (!panel || !btn) {
            return;
        }

        const wasOpen = panel.classList.contains('is-open');
        closeAll();

        if (wasOpen) {
            return;
        }

        panel.classList.add('is-open');
        btn.setAttribute('aria-expanded', 'true');
        window.BeautageHeaderNav.openId = root.getAttribute('data-nav-dropdown');
        document.getElementById(STRIP_ID)?.classList.add('mega-open');

        if (!isDesktop()) {
            document.body.classList.add('storefront-nav-open');
            positionMobilePanel(root, panel);
            window.requestAnimationFrame(() => positionMobilePanel(root, panel));
        }
    }

    window.BeautageHeaderNav = {
        openId: null,
        toggle(rootOrId) {
            const root =
                typeof rootOrId === 'string'
                    ? document.querySelector(`[data-nav-dropdown="${rootOrId}"]`)
                    : rootOrId;
            toggle(root);
        },
        closeAll,
    };

    function bindToggles() {
        document.querySelectorAll('[data-nav-dropdown-toggle]').forEach((btn) => {
            if (btn.dataset.navBound === '1') {
                return;
            }
            btn.dataset.navBound = '1';

            const activate = (event) => {
                event.preventDefault();
                event.stopPropagation();
                toggle(btn.closest('[data-nav-dropdown]'));
            };

            btn.addEventListener('click', activate);
            btn.addEventListener('touchend', (event) => {
                if (event.cancelable) {
                    event.preventDefault();
                }
                activate(event);
            }, { passive: false });
        });
    }

    function init() {
        bindToggles();
        const backdrop = document.getElementById('storefront-nav-backdrop');
        if (backdrop && backdrop.parentElement !== document.body) {
            document.body.appendChild(backdrop);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    document.addEventListener('click', (event) => {
        if (event.target.closest('[data-nav-dropdown-toggle]')) {
            return;
        }
        if (event.target.id === 'storefront-nav-backdrop') {
            closeAll();

            return;
        }
        if (!event.target.closest('[data-nav-dropdown]')) {
            closeAll();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeAll();
        }
    });

    window.addEventListener(
        'resize',
        () => {
            if (window.BeautageHeaderNav.openId) {
                const root = document.querySelector(
                    `[data-nav-dropdown="${window.BeautageHeaderNav.openId}"]`
                );
                const panel = root ? getPanel(root) : null;
                if (root && panel?.classList.contains('is-open')) {
                    positionMobilePanel(root, panel);
                }
            }
            if (isDesktop()) {
                document.getElementById(STRIP_ID)?.classList.remove('mega-open');
                document.body.classList.remove('storefront-nav-open');
            }
        },
        { passive: true }
    );

    window.addEventListener(
        'scroll',
        () => {
            if (!window.BeautageHeaderNav.openId || isDesktop()) {
                return;
            }
            const root = document.querySelector(
                `[data-nav-dropdown="${window.BeautageHeaderNav.openId}"]`
            );
            const panel = root ? getPanel(root) : null;
            if (root && panel?.classList.contains('is-open')) {
                positionMobilePanel(root, panel);
            }
        },
        { passive: true }
    );
})();
