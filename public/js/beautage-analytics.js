(function () {
    const cfg = window.BEAUTAGE_ANALYTICS || {};
    const currency = cfg.currency || 'PKR';
    let vendorsInitStarted = false;

    function whenIdle(callback) {
        if (typeof window.requestIdleCallback === 'function') {
            window.requestIdleCallback(callback, { timeout: 3500 });
            return;
        }
        window.setTimeout(callback, 1200);
    }

    function initVendors() {
        if (vendorsInitStarted) {
            return;
        }
        vendorsInitStarted = true;

        if (cfg.gaId) {
            window.dataLayer = window.dataLayer || [];
            window.gtag =
                window.gtag ||
                function gtag() {
                    window.dataLayer.push(arguments);
                };
            const gaScript = document.createElement('script');
            gaScript.async = true;
            gaScript.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(cfg.gaId);
            document.head.appendChild(gaScript);
            window.gtag('js', new Date());
            window.gtag('config', cfg.gaId, {
                anonymize_ip: true,
                send_page_view: true,
                allow_google_signals: true,
            });
        } else if (cfg.metaPixelId) {
            window.dataLayer = window.dataLayer || [];
        }

        if (cfg.metaPixelId) {
            !(function (f, b, e, v, n, t, s) {
                if (f.fbq) {
                    return;
                }
                n = f.fbq = function () {
                    n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
                };
                if (!f._fbq) {
                    f._fbq = n;
                }
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s);
            })(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
            window.fbq('init', cfg.metaPixelId);
            window.fbq('track', 'PageView');
        }
    }

    function enabled() {
        return Boolean(cfg.gaId || cfg.metaPixelId);
    }

    function gaItems(items) {
        return (items || []).map((item) => ({
            item_id: String(item.id || item.sku || item.name || 'item'),
            item_name: String(item.name || 'Product'),
            price: Number(item.price || 0),
            quantity: Number(item.qty || item.quantity || 1),
        }));
    }

    function lineValue(items) {
        return (items || []).reduce((sum, item) => {
            const qty = Number(item.qty || item.quantity || 1);
            return sum + Number(item.price || 0) * qty;
        }, 0);
    }

    function pushDataLayer(eventName, ecommerce) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: eventName,
            ecommerce: ecommerce || {},
        });
    }

    function gtagEvent(name, params) {
        if (typeof window.gtag !== 'function' || !cfg.gaId) {
            return;
        }
        window.gtag('event', name, params);
    }

    function fbqEvent(name, params, eventId) {
        if (typeof window.fbq !== 'function' || !cfg.metaPixelId) {
            return;
        }
        if (eventId) {
            window.fbq('track', name, params, { eventID: eventId });
        } else {
            window.fbq('track', name, params);
        }
    }

    function trackViewItem(product) {
        if (!enabled() || !product) {
            return;
        }
        const items = gaItems([{ id: product.id, name: product.name, price: product.price, qty: 1 }]);
        const value = Number(product.price || 0);
        pushDataLayer('view_item', { currency, value, items });
        gtagEvent('view_item', { currency, value, items });
        fbqEvent('ViewContent', {
            content_ids: [String(product.id)],
            content_name: product.name,
            content_type: 'product',
            value,
            currency,
        });
    }

    function trackAddToCart(item) {
        if (!enabled() || !item) {
            return;
        }
        const items = gaItems([item]);
        const value = Number(item.price || 0) * Number(item.qty || 1);
        pushDataLayer('add_to_cart', { currency, value, items });
        gtagEvent('add_to_cart', { currency, value, items });
        fbqEvent('AddToCart', {
            content_ids: [String(item.id)],
            content_name: item.name,
            content_type: 'product',
            value,
            currency,
        });
    }

    function trackBeginCheckout(cartItems) {
        if (!enabled() || !cartItems || !cartItems.length) {
            return;
        }
        const items = gaItems(cartItems);
        const value = lineValue(cartItems);
        pushDataLayer('begin_checkout', { currency, value, items });
        gtagEvent('begin_checkout', { currency, value, items });
        fbqEvent('InitiateCheckout', {
            content_ids: items.map((i) => i.item_id),
            num_items: items.reduce((s, i) => s + i.quantity, 0),
            value,
            currency,
        });
    }

    function trackPurchase(payload) {
        if (!enabled() || !payload) {
            return;
        }
        const items = gaItems(payload.items || []);
        const value = Number(payload.value || lineValue(payload.items || []));
        const transactionId = String(payload.transaction_id || payload.order_number || '');
        pushDataLayer('purchase', {
            currency,
            value,
            transaction_id: transactionId,
            items,
        });
        gtagEvent('purchase', {
            currency,
            value,
            transaction_id: transactionId,
            items,
        });
        fbqEvent(
            'Purchase',
            {
                content_ids: items.map((i) => i.item_id),
                content_type: 'product',
                value,
                currency,
            },
            transactionId,
        );
    }

    window.BeautageAnalytics = {
        trackViewItem,
        trackAddToCart,
        trackBeginCheckout,
        trackPurchase,
    };

    document.addEventListener('DOMContentLoaded', () => {
        whenIdle(() => {
            initVendors();
            if (window.currentProduct) {
                trackViewItem(window.currentProduct);
            }
            if (document.getElementById('checkout-form') && window.BeautageCart) {
                const cart = BeautageCart.readStorage();
                if (cart.length) {
                    trackBeginCheckout(cart);
                }
            }
        });
    });

    ['scroll', 'pointerdown', 'keydown', 'touchstart'].forEach((eventName) => {
        window.addEventListener(
            eventName,
            () => {
                initVendors();
            },
            { once: true, passive: true },
        );
    });
})();
