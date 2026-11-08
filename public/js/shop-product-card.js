(function () {
    const PLACEHOLDER = 'https://placehold.co/640x640/0B132B/D4AF37?text=Shop+Pakistan';

    function esc(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function money(amount) {
        if (window.BeautageMoney) {
            return window.BeautageMoney.formatDecimal(amount);
        }

        return 'Rs. ' + Number(amount || 0).toFixed(0);
    }

    function excerpt(product) {
        const source = product.short || product.short_description || product.desc || '';
        const clean = String(source).replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim();
        if (clean.length <= 110) {
            return clean;
        }

        return clean.slice(0, 107).trim() + '…';
    }

    function productUrl(product) {
        if (product.url) {
            return product.url;
        }
        if (product.slug) {
            return '/products/' + product.slug;
        }

        return '/collections';
    }

    function html(product) {
        const name = esc(product.name || 'Product');
        const href = esc(productUrl(product));
        const image = esc(product.image || PLACEHOLDER);
        const hoverRaw = String(product.hoverImage || product.hover_image || '').trim();
        const hover = hoverRaw && hoverRaw !== (product.image || '') ? esc(hoverRaw) : '';
        const category = product.category ? '<p class="lv2-muted">' + esc(product.category) + '</p>' : '';
        const blurb = excerpt(product);
        const desc = blurb ? '<p class="lv2-card-desc">' + esc(blurb) + '</p>' : '';
        const price = Number(product.price || 0);
        const oldPrice = Number(product.oldPrice || product.old_price || 0);
        const save = oldPrice > price && oldPrice > 0 ? Math.round((1 - price / oldPrice) * 100) : 0;
        const reviews = Number(product.reviews || 0);
        const full = Math.max(0, Math.min(5, Math.round(Number(product.rating) || 0)));
        const stars = reviews > 0
            ? '<p class="lv2-stars">' + '★'.repeat(full) + '☆'.repeat(5 - full) + ' <span class="lv2-muted">(' + reviews + ')</span></p>'
            : '';
        const inStock = product.inStock !== false && product.inStock !== 0 && product.inStock !== '0' && product.in_stock !== false;
        const attrs = [
            'data-id="' + esc(product.id) + '"',
            'data-name="' + name + '"',
            'data-price="' + esc(price) + '"',
            'data-old="' + esc(oldPrice > price ? oldPrice : '') + '"',
            'data-image="' + image + '"',
            'data-category="' + esc(product.category || '') + '"',
            'data-desc="' + esc(blurb) + '"',
            'data-url="' + href + '"',
            'data-stock="' + (inStock ? '1' : '0') + '"',
        ].join(' ');

        return ''
            + '<article class="lv2-product">'
            + '<a href="' + href + '" class="lv2-product-media">'
            + '<img class="lv2-card-photo" src="' + image + '" alt="' + name + '" width="640" height="640" loading="lazy" decoding="async" onerror="this.onerror=null;this.src=\'' + PLACEHOLDER + '\';">'
            + (hover ? '<img class="lv2-card-photo lv2-card-photo--alt" src="' + hover + '" alt="" width="640" height="640" loading="lazy" decoding="async">' : '')
            + (save ? '<span class="lv2-save">SAVE ' + save + '%</span>' : '')
            + '</a>'
            + '<div class="lv2-product-copy">'
            + category
            + '<h3><a href="' + href + '">' + name + '</a></h3>'
            + desc
            + stars
            + '<p class="lv2-price-row"><span class="lv2-price">' + esc(money(price)) + '</span>'
            + (oldPrice > price ? '<span class="lv2-old">' + esc(money(oldPrice)) + '</span>' : '')
            + '</p></div>'
            + '<div class="lv2-card-actions">'
            + '<button type="button" class="lv2-card-quick" data-quick-view ' + attrs + '>Quick view</button>'
            + (inStock
                ? '<button type="button" class="lv2-btn-navy" data-card-add ' + attrs + '>Add to cart</button>'
                : '<button type="button" class="lv2-btn-navy" disabled>Out of stock</button>')
            + '</div></article>';
    }

    function read(el) {
        return {
            id: el.getAttribute('data-id'),
            name: el.getAttribute('data-name') || 'Product',
            price: el.getAttribute('data-price') || 0,
            oldPrice: el.getAttribute('data-old') || 0,
            image: el.getAttribute('data-image') || PLACEHOLDER,
            category: el.getAttribute('data-category') || '',
            desc: el.getAttribute('data-desc') || '',
            url: el.getAttribute('data-url') || '/collections',
            inStock: el.getAttribute('data-stock') !== '0',
        };
    }

    function addCard(card) {
        const id = Number(card.id);
        const catalog = Array.isArray(window.products) ? window.products : [];
        const known = catalog.some(function (item) { return Number(item.id) === id; });

        if (known && typeof window.addToCart === 'function') {
            window.addToCart(id, 1);
            return;
        }

        if (window.BeautageCart) {
            window.BeautageCart.addItem({
                id: id,
                name: card.name,
                price: Number(card.price || 0),
                image: card.image,
                qty: 1,
            });
            if (typeof window.showToast === 'function') {
                window.showToast('Added "' + card.name + '" to your bag.');
            }
        }
    }

    function openQuickView(card) {
        const modal = document.getElementById('quickview-modal');
        const content = document.getElementById('quickview-content');
        if (!modal || !content) {
            window.location.href = card.url;
            return;
        }

        const price = Number(card.price || 0);
        const oldPrice = Number(card.oldPrice || 0);
        const stock = card.inStock
            ? '<span class="sp-qv-stock">In stock</span>'
            : '<span class="sp-qv-stock sp-qv-stock--out">Out of stock</span>';

        content.innerHTML = ''
            + '<div class="sp-qv-media"><img src="' + esc(card.image) + '" alt="' + esc(card.name) + '"></div>'
            + '<div class="sp-qv-copy">'
            + (card.category ? '<p class="sp-qv-cat">' + esc(card.category) + '</p>' : '')
            + '<h2>' + esc(card.name) + '</h2>'
            + '<p class="sp-qv-price"><strong>' + esc(money(price)) + '</strong>'
            + (oldPrice > price ? '<s>' + esc(money(oldPrice)) + '</s>' : '')
            + stock
            + '</p>'
            + (card.desc ? '<p class="sp-qv-desc">' + esc(card.desc) + '</p>' : '')
            + '<div class="sp-qv-actions">'
            + (card.inStock ? '<button type="button" class="lv2-btn-navy" data-card-add data-id="' + esc(card.id) + '" data-name="' + esc(card.name) + '" data-price="' + esc(price) + '" data-image="' + esc(card.image) + '">Add to cart</button>' : '')
            + '<a class="lv2-card-quick" href="' + esc(card.url) + '">View product</a>'
            + '</div></div>';

        modal.classList.remove('hidden');
    }

    function closeQuickView() {
        const modal = document.getElementById('quickview-modal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }

    document.addEventListener('click', function (event) {
        const quick = event.target.closest('[data-quick-view]');
        if (quick) {
            event.preventDefault();
            openQuickView(read(quick));
            return;
        }

        const add = event.target.closest('[data-card-add]');
        if (add && !add.disabled) {
            event.preventDefault();
            addCard(read(add));
            if (add.closest('#quickview-content')) {
                closeQuickView();
            }
        }
    });

    window.closeQuickView = closeQuickView;
    window.ShopProductCard = { html: html, open: openQuickView };
})();
