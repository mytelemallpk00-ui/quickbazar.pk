(function () {
    const STORAGE_KEY = 'beautage_recently_viewed';
    const MAX_ITEMS = 12;

    function dom() {
        return window.BeautageDom || {
            escapeHtml: (v) => String(v ?? ''),
            escapeAttr: (v) => String(v ?? ''),
            whenIdle: (cb) => window.setTimeout(cb, 1),
            PLACEHOLDER_PRODUCT: 'https://placehold.co/400x400/5D267D/FFFFFF?text=Beautage',
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
            localStorage.setItem(STORAGE_KEY, JSON.stringify(items.slice(0, MAX_ITEMS)));
        } catch (e) {
            /* quota */
        }
    }

    function normalize(product) {
        if (!product || product.id == null) {
            return null;
        }

        const slug = product.slug || product.productSlug || '';
        const url = product.url || (slug ? `/products/${slug}` : null);

        return {
            id: Number(product.id),
            slug,
            url,
            name: String(product.name || 'Product'),
            price: Number(product.price || 0),
            oldPrice: product.oldPrice != null ? Number(product.oldPrice) : null,
            image: product.image || product.hoverImage || '',
            category: product.category || '',
            inStock: product.inStock !== false && product.in_stock !== false,
            viewedAt: Date.now(),
        };
    }

    function record(product) {
        const entry = normalize(product);
        if (!entry) {
            return;
        }

        const list = readStorage().filter((item) => Number(item.id) !== entry.id);
        list.unshift(entry);
        writeStorage(list);
    }

    function list(options) {
        const excludeId = options?.excludeId != null ? Number(options.excludeId) : null;
        const limit = options?.limit != null ? Number(options.limit) : 8;

        return readStorage()
            .filter((item) => (excludeId ? Number(item.id) !== excludeId : true))
            .slice(0, Math.max(1, limit));
    }

    function formatMoney(amount) {
        if (window.BeautageMoney) {
            return window.BeautageMoney.formatDecimal(amount);
        }

        return `Rs. ${Number(amount || 0).toFixed(0)}`;
    }

    function productUrl(item) {
        if (item.url) {
            return item.url;
        }
        if (item.slug) {
            return `/products/${item.slug}`;
        }

        return '#';
    }

    function renderSection(sectionEl) {
        if (!sectionEl) {
            return;
        }

        const grid = sectionEl.querySelector('[data-recently-viewed-grid]');
        if (!grid) {
            return;
        }

        const { escapeHtml, escapeAttr, PLACEHOLDER_PRODUCT } = dom();
        const excludeId = sectionEl.dataset.excludeId ? Number(sectionEl.dataset.excludeId) : null;
        const limit = sectionEl.dataset.limit ? Number(sectionEl.dataset.limit) : 8;
        const items = list({ excludeId, limit });

        if (!items.length) {
            sectionEl.classList.add('hidden');
            grid.innerHTML = '';
            return;
        }

        sectionEl.classList.remove('hidden');

        grid.innerHTML = items.map((item) => {
            const name = escapeHtml(item.name);
            const href = escapeAttr(productUrl(item));
            const img = escapeAttr(item.image || PLACEHOLDER_PRODUCT);
            const stockBadge = item.inStock
                ? '<span class="text-[9px] font-bold text-emerald-600">In stock</span>'
                : '<span class="text-[9px] font-bold text-amber-600">Out of stock</span>';

            const price = Number(item.price || 0);
            const oldPrice = Number(item.oldPrice || 0);
            const save = oldPrice > price && oldPrice > 0 ? Math.round((1 - price / oldPrice) * 100) : 0;
            const inStock = item.inStock !== false && item.inStock !== 0 && item.inStock !== '0';

            return `
                <article class="lv2-product">
                    <a href="${href}" class="lv2-product-media">
                        <img src="${img}" alt="${name}" width="480" height="480" loading="lazy" decoding="async">
                        ${save ? `<span class="lv2-save">SAVE ${save}%</span>` : ''}
                    </a>
                    <div>
                        <h3><a href="${href}">${name}</a></h3>
                        <p class="lv2-price-row">
                            <span class="lv2-price">${formatMoney(price)}</span>
                            ${oldPrice > price ? `<span class="lv2-old">${formatMoney(oldPrice)}</span>` : ''}
                        </p>
                        ${stockBadge}
                    </div>
                    <button type="button" data-recently-add="${Number(item.id)}" class="lv2-btn-navy" style="margin-top:0.8rem" ${inStock ? '' : 'disabled'}>${inStock ? 'Add to cart' : 'Out of stock'}</button>
                </article>
            `;
        }).join('');

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function renderAll() {
        document.querySelectorAll('[data-recently-viewed-section]').forEach((section) => renderSection(section));
    }

    function quickAdd(productId) {
        const id = Number(productId);
        const fromCatalog = Array.isArray(window.products)
            ? window.products.find((p) => Number(p.id) === id)
            : null;
        const fromRecent = readStorage().find((p) => Number(p.id) === id);
        const source = fromCatalog || fromRecent;

        if (!source) {
            return;
        }

        if (typeof window.addToCart === 'function' && fromCatalog) {
            window.addToCart(id, 1);
            return;
        }

        if (typeof window.addToCartDirect === 'function') {
            window.addToCartDirect({
                id,
                name: source.name,
                price: source.price,
                image: source.image,
                qty: 1,
            });
            if (typeof window.showToast === 'function') {
                window.showToast('Added to bag');
            }
        }
    }

    function bootRecord() {
        if (window.currentProduct && window.currentProduct.id) {
            record({
                ...window.currentProduct,
                slug: window.currentProduct.slug || window.BEAUTAGE_PRODUCT_SLUG || '',
            });
        }
    }

    document.addEventListener('click', (event) => {
        const btn = event.target.closest('[data-recently-add]');
        if (!btn) {
            return;
        }
        quickAdd(btn.getAttribute('data-recently-add'));
    });

    document.addEventListener('DOMContentLoaded', () => {
        bootRecord();
        dom().whenIdle(renderAll);
    });

    window.BeautageRecentlyViewed = {
        record,
        list,
        renderAll,
    };
})();
