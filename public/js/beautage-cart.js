(function () {
    const STORAGE_KEY = 'beautage_cart';
    const EMAIL_KEY = 'beautage_cart_email';
    let syncTimer = null;

    function dom() {
        return window.BeautageDom || {
            escapeHtml: (v) => String(v ?? ''),
            PLACEHOLDER_THUMB: 'https://placehold.co/100x100/5D267D/FFFFFF?text=Item',
        };
    }

    function readStorage() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) {
                return [];
            }
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed : [];
        } catch (e) {
            return [];
        }
    }

    function writeStorage(items) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
        } catch (e) {
            /* quota */
        }
    }

    function formatMoney(amount) {
        if (window.BeautageMoney) {
            return window.BeautageMoney.formatDecimal(amount);
        }

        return `Rs. ${Number(amount || 0).toFixed(0)}`;
    }

    function cartSubtotal(cart) {
        return cart.reduce((sum, item) => {
            if (window.BeautageMoney) {
                return sum + window.BeautageMoney.lineTotal(item);
            }

            return sum + Number(item.price || 0) * Number(item.qty || 1);
        }, 0);
    }

    function isCheckoutPage() {
        return Boolean(document.getElementById('checkout-form'));
    }

    function renderCartItemHtml(item) {
        const { escapeHtml, PLACEHOLDER_THUMB } = dom();
        const name = escapeHtml(item.name);
        const image = escapeHtml(item.image || PLACEHOLDER_THUMB);
        const id = Number(item.id);

        return `
            <div class="flex items-center gap-4 p-3 rounded-2xl bg-purple-50/50 border border-purple-100">
                <img src="${image}" alt="${name}" width="64" height="64" loading="lazy" decoding="async" class="w-16 h-16 object-cover rounded-xl" onerror="this.onerror=null; this.src='${PLACEHOLDER_THUMB}';">
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs font-bold text-beautage-purple truncate">${name}</h4>
                    <span class="text-xs font-extrabold text-slate-800">${formatMoney(item.price)}</span>
                    <div class="flex items-center gap-3 mt-2">
                        <div class="flex items-center border border-purple-200 rounded-lg bg-white overflow-hidden">
                            <button type="button" data-cart-qty-minus="${id}" class="px-2 py-0.5 text-xs font-bold text-slate-600 hover:bg-purple-50 min-w-[28px] min-h-[28px]">-</button>
                            <span class="px-2.5 py-0.5 text-xs font-bold text-slate-800">${Number(item.qty || 1)}</span>
                            <button type="button" data-cart-qty-plus="${id}" class="px-2 py-0.5 text-xs font-bold text-slate-600 hover:bg-purple-50 min-w-[28px] min-h-[28px]">+</button>
                        </div>
                        <button type="button" data-cart-remove="${id}" class="text-rose-500 hover:text-rose-700 text-xs font-semibold min-h-[28px]">Remove</button>
                    </div>
                </div>
            </div>
        `;
    }

    function updateCartUI() {
        const cart = readStorage();
        const totalCount = cart.reduce((sum, item) => sum + (item.qty || 0), 0);
        const subtotal = cartSubtotal(cart);
        const tax = subtotal * 0.08;
        const grandTotal = subtotal + tax;

        ['cart-badge', 'cart-badge-desktop', 'mobile-cart-badge'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = String(totalCount);
            }
        });

        const headerTotal = document.getElementById('cart-total-header');
        if (headerTotal) {
            headerTotal.textContent = formatMoney(subtotal);
        }

        if (!isCheckoutPage()) {
            const subEl = document.getElementById('cart-subtotal');
            if (subEl) {
                subEl.textContent = formatMoney(subtotal);
            }
            const taxEl = document.getElementById('cart-tax');
            if (taxEl) {
                taxEl.textContent = formatMoney(tax);
            }
            const totalEl = document.getElementById('cart-total');
            if (totalEl) {
                totalEl.textContent = formatMoney(grandTotal);
            }
        }

        const container = document.getElementById('cart-items');
        if (container) {
            container.innerHTML = cart.length === 0
                ? '<div class="text-center py-12 text-slate-400 text-xs">Your shopping bag is currently empty.</div>'
                : cart.map(renderCartItemHtml).join('');
        }

        if (typeof window.renderCheckoutSummary === 'function') {
            window.checkoutCart = cart;
            window.renderCheckoutSummary();
        }

        scheduleAbandonedCartSync(cart);
    }

    function resolveCartEmail() {
        const checkoutEmail = document.getElementById('cust-email');
        if (checkoutEmail && checkoutEmail.value.trim()) {
            return checkoutEmail.value.trim();
        }
        try {
            return localStorage.getItem(EMAIL_KEY) || '';
        } catch (e) {
            return '';
        }
    }

    function persistCartEmail(email) {
        if (!email) {
            return;
        }
        try {
            localStorage.setItem(EMAIL_KEY, email);
        } catch (e) {
            /* ignore */
        }
    }

    function scheduleAbandonedCartSync(cart) {
        if (!cart || cart.length === 0) {
            return;
        }
        clearTimeout(syncTimer);
        syncTimer = setTimeout(() => syncAbandonedCart(cart), 2500);
    }

    function syncAbandonedCart(cart) {
        const email = resolveCartEmail();
        if (!email || !Array.isArray(cart) || cart.length === 0) {
            return;
        }
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {
            return;
        }
        const payload = {
            email,
            items: cart.map((item) => ({
                id: item.id,
                name: item.name,
                price: Number(item.price || 0),
                qty: Number(item.qty || 1),
                image: item.image || null,
            })),
        };
        fetch('/store/cart/sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': token.getAttribute('content'),
            },
            body: JSON.stringify(payload),
            credentials: 'same-origin',
        }).catch(() => {});
    }

    function addItem(item) {
        const cart = readStorage();
        const normalized = {
            ...item,
            price: Number(item.price || 0),
            qty: Math.max(1, Number(item.qty || 1)),
        };
        const existing = cart.find((i) => i.id === normalized.id && i.name === normalized.name);
        if (existing) {
            existing.qty += normalized.qty;
        } else {
            cart.push(normalized);
        }
        writeStorage(cart);
        updateCartUI();
        if (window.BeautageAnalytics) {
            window.BeautageAnalytics.trackAddToCart(normalized);
        }
    }

    function addProductById(productId, qty) {
        const catalog = window.products;
        if (!Array.isArray(catalog)) {
            return false;
        }
        const product = catalog.find((p) => Number(p.id) === Number(productId));
        if (!product) {
            return false;
        }
        addItem({
            id: product.id,
            name: product.name,
            price: product.price,
            image: product.image,
            qty: qty || 1,
        });

        return true;
    }

    function removeItem(productId) {
        writeStorage(readStorage().filter((i) => Number(i.id) !== Number(productId)));
        updateCartUI();
    }

    function updateQty(productId, delta) {
        const cart = readStorage();
        const item = cart.find((i) => Number(i.id) === Number(productId));
        if (!item) {
            return;
        }
        item.qty += delta;
        if (item.qty <= 0) {
            removeItem(productId);
            return;
        }
        writeStorage(cart);
        updateCartUI();
    }

    function clearCart() {
        writeStorage([]);
        updateCartUI();
    }

    function syncPageCartVariable() {
        if (typeof window.cart !== 'undefined') {
            window.cart = readStorage();
        }
    }

    function addToCart(productId, qty) {
        const ok = addProductById(productId, qty);
        if (ok && typeof window.showToast === 'function') {
            window.showToast('Added to bag');
        }

        return ok;
    }

    document.addEventListener('click', (event) => {
        const target = event.target.closest('[data-cart-qty-minus], [data-cart-qty-plus], [data-cart-remove]');
        if (!target) {
            return;
        }
        const minus = target.getAttribute('data-cart-qty-minus');
        const plus = target.getAttribute('data-cart-qty-plus');
        const remove = target.getAttribute('data-cart-remove');
        if (minus) {
            updateQty(Number(minus), -1);
        } else if (plus) {
            updateQty(Number(plus), 1);
        } else if (remove) {
            removeItem(Number(remove));
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        syncPageCartVariable();
        updateCartUI();
        const emailInput = document.getElementById('cust-email');
        if (emailInput) {
            emailInput.addEventListener('blur', () => {
                persistCartEmail(emailInput.value.trim());
                scheduleAbandonedCartSync(readStorage());
            });
        }
    });

    window.BeautageCart = {
        readStorage,
        writeStorage,
        updateCartUI,
        addItem,
        addProductById,
        removeItem,
        updateQty,
        clearCart,
        syncPageCartVariable,
    };

    window.addToCartDirect = addItem;
    window.addToCart = addToCart;
})();
