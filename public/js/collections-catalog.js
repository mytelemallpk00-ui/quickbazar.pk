(function () {
    const PAGE_SIZE = 12;
    const config = window.CATALOG_PAGE || {};
    const categories = Array.isArray(config.categories) ? config.categories : [];
    const priceFloor = Number(config.priceFloor ?? 0);
    const priceCeil = Number(config.priceCeil ?? 0);
    const defaultTitle = config.defaultTitle || 'Collections & Categories';
    const defaultBlurb = config.defaultBlurb || '';
    const placeholder = 'https://placehold.co/300x300/0B132B/D4AF37?text=Quick+Bazar';
    const ingredients = [
        { id: 'biotin', label: 'Organic Biotin', test: /biotin/i },
        { id: 'argan', label: 'Argan Extract', test: /argan/i },
        { id: 'rosemary', label: 'Rosemary Mint', test: /rosemary/i },
        { id: 'keratin', label: 'Keratin Protein', test: /keratin/i },
        { id: 'collagen', label: 'Collagen', test: /collagen/i },
        { id: 'coconut', label: 'Coconut', test: /coconut/i },
        { id: 'shea', label: 'Shea Butter', test: /shea/i },
        { id: 'hyaluronic', label: 'Hyaluronic Acid', test: /hyaluronic/i },
        { id: 'tea-tree', label: 'Tea Tree', test: /tea\s*tree/i },
        { id: 'vitamin-c', label: 'Vitamin C', test: /vitamin\s*c/i },
    ];

    const source = (Array.isArray(window.products) ? window.products : []).map(function (product, index) {
        return Object.assign({}, product, { _order: index });
    });

    let wishlist = [];
    let filtered = source.slice();
    let page = 1;
    let searchQuery = '';

    function esc(value) {
        if (window.BeautageDom && typeof window.BeautageDom.escapeHtml === 'function') {
            return window.BeautageDom.escapeHtml(value);
        }
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function money(amount) {
        if (window.BeautageMoney) {
            return window.BeautageMoney.formatDecimal(amount);
        }
        const value = Number(amount || 0);
        return 'Rs. ' + value.toLocaleString('en-PK');
    }

    function normalize(value) {
        return String(value || '').toLowerCase().replace(/[^a-z0-9]+/g, '');
    }

    function categoryBySlug(slug) {
        return categories.find(function (entry) { return entry.slug === slug; }) || null;
    }

    function productInCategory(product, slug) {
        const category = categoryBySlug(slug);
        if (product.categoryId != null && category && category.id != null && Number(product.categoryId) === Number(category.id)) {
            return true;
        }
        if (product.categorySlug && product.categorySlug === slug) {
            return true;
        }
        if (category && category.name && normalize(product.category) === normalize(category.name)) {
            return true;
        }
        return normalize(product.category) === normalize(slug) || normalize(product.categorySlug) === normalize(slug);
    }

    function productText(product) {
        return [product.name, product.desc, product.category, product.badge].filter(Boolean).join(' ');
    }

    function checkedValues(name) {
        return Array.prototype.map.call(
            document.querySelectorAll('input[name="' + name + '"]:checked'),
            function (input) { return input.value; }
        );
    }

    function readPrice(id, fallback) {
        const input = document.getElementById(id);
        if (!input || input.value === '') {
            return fallback;
        }
        const value = Number(input.value);
        return Number.isFinite(value) ? value : fallback;
    }

    function badgeMeta(product) {
        let label = String(product.badge || '').trim();
        const oldPrice = Number(product.oldPrice || 0);
        const price = Number(product.price || 0);
        if (!label && oldPrice > price && oldPrice > 0) {
            label = Math.round((1 - price / oldPrice) * 100) + '% OFF';
        }
        if (!label) {
            return null;
        }
        const token = label.toUpperCase();
        let tone = 'gold';
        if (token.indexOf('OFF') !== -1 || token.indexOf('%') !== -1) {
            tone = 'sale';
        } else if (token.indexOf('NEW') !== -1) {
            tone = 'new';
        } else if (token.indexOf('ORGANIC') !== -1 || token.indexOf('POPULAR') !== -1) {
            tone = 'organic';
        } else if (token.indexOf('BUNDLE') !== -1) {
            tone = 'bundle';
        }
        return { label: label, tone: tone };
    }

    function starsHtml(rating) {
        const value = Math.max(0, Math.min(5, Number(rating) || 0));
        const shown = value.toFixed(1);
        return '<span class="lcat-stars" style="--lcat-rating:' + shown + '" aria-label="Rated ' + shown + ' out of 5"><span aria-hidden="true">★★★★★</span><span class="lcat-stars-fill" aria-hidden="true">★★★★★</span></span>';
    }

    function cardHtml(product) {
        const id = Number(product.id);
        const saved = wishlist.some(function (item) { return Number(item.id) === id; });
        const badge = badgeMeta(product);
        const inStock = product.inStock !== false && product.inStock !== 0 && product.inStock !== '0';
        const oldPrice = Number(product.oldPrice || 0);
        const price = Number(product.price || 0);
        const image = esc(product.image || placeholder);
        const hover = product.hoverImage && product.hoverImage !== product.image ? esc(product.hoverImage) : '';
        const slug = encodeURIComponent(product.slug || '');
        const name = esc(product.name || 'Product');
        const category = esc(product.category || 'Collection');
        const reviews = Number(product.reviews || 0);

        return ''
            + '<article class="lcat-card">'
            + '<div class="lcat-media-wrap">'
            + (badge ? '<span class="lcat-badge lcat-badge-' + badge.tone + '">' + esc(badge.label) + '</span>' : '')
            + '<button type="button" class="lcat-wish' + (saved ? ' is-saved' : '') + '" data-wish="' + id + '" aria-label="' + (saved ? 'Remove from wishlist' : 'Save to wishlist') + '" aria-pressed="' + (saved ? 'true' : 'false') + '"><i class="fa-' + (saved ? 'solid' : 'regular') + ' fa-heart"></i></button>'
            + '<a class="lcat-media" href="/products/' + slug + '">'
            + '<span class="lcat-media-stack">'
            + '<img class="lcat-img" src="' + image + '" alt="' + name + '" loading="lazy" decoding="async" onerror="this.onerror=null;this.src=\'' + placeholder + '\';">'
            + (hover ? '<img class="lcat-img lcat-img-hover" src="' + hover + '" alt="" loading="lazy" decoding="async" onerror="this.remove();">' : '')
            + '</span></a></div>'
            + '<div>'
            + '<span class="lcat-cat">' + category + '</span>'
            + '<h3 class="lcat-name"><a href="/products/' + slug + '">' + name + '</a></h3>'
            + '<div class="lcat-rating">' + starsHtml(product.rating) + '<span class="lcat-reviews">(' + reviews.toLocaleString('en-PK') + ')</span></div>'
            + '<div class="lcat-buy"><div><span class="lcat-price">' + esc(money(price)) + '</span>'
            + (oldPrice > price ? '<span class="lcat-old">' + esc(money(oldPrice)) + '</span>' : '')
            + '</div><button type="button" class="lcat-add" data-add="' + id + '"' + (inStock ? '' : ' disabled') + '><i class="fa-solid fa-plus"></i> ' + (inStock ? 'Add' : 'Sold out') + '</button></div>'
            + '</div></article>';
    }

    function updateHero(selected) {
        const title = document.getElementById('catalog-hero-title');
        const blurb = document.getElementById('catalog-hero-blurb');
        const crumb = document.getElementById('catalog-hero-crumb');
        const count = document.getElementById('catalog-hero-count');
        const noun = document.getElementById('lcat-result-noun');
        let heading = defaultTitle;
        let copy = defaultBlurb;
        let crumbText = 'All departments';
        let nounText = 'products';

        if (selected.length === 1) {
            const category = categoryBySlug(selected[0]);
            if (category) {
                heading = category.label || category.name || heading;
                copy = category.description || defaultBlurb;
                crumbText = heading;
                nounText = heading + ' products';
            }
        } else if (selected.length > 1) {
            crumbText = 'Selected departments';
            nounText = 'selected products';
        }

        if (title) title.textContent = heading;
        if (blurb) blurb.textContent = copy;
        if (crumb) crumb.textContent = crumbText;
        if (count) count.textContent = filtered.length + ' Active Formulas';
        if (noun) noun.textContent = nounText;
    }

    function updateFilterCount(selected, minPrice, maxPrice, ingredientIds, minRating, inStockOnly) {
        let count = selected.length + ingredientIds.length;
        if (minPrice > priceFloor || maxPrice < priceCeil) count += 1;
        if (minRating > 0) count += 1;
        if (inStockOnly) count += 1;
        const target = document.getElementById('lcat-active-filter-count');
        if (target) target.textContent = String(count);
    }

    function syncUrl(selected) {
        try {
            const url = new URL(window.location.href);
            if (selected.length) {
                url.searchParams.set('cat', selected.join(','));
            } else {
                url.searchParams.delete('cat');
            }
            window.history.replaceState({}, '', url.pathname + url.search);
        } catch (error) {
            /* ignore */
        }
    }

    function syncHeaderCategory(selected) {
        const select = document.getElementById('search-category');
        if (!select) return;
        if (selected.length === 1 && select.querySelector('option[value="' + selected[0].replace(/"/g, '') + '"]')) {
            select.value = selected[0];
        } else if (!selected.length) {
            select.value = 'all';
        }
    }

    function renderPager() {
        const pager = document.getElementById('lcat-pages');
        const currentEl = document.getElementById('lcat-page-current');
        const totalEl = document.getElementById('lcat-page-total');
        const pages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
        if (page > pages) page = pages;
        if (currentEl) currentEl.textContent = String(filtered.length ? page : 0);
        if (totalEl) totalEl.textContent = String(filtered.length ? pages : 0);
        if (!pager) return;

        const buttons = [];
        buttons.push('<button type="button" class="lcat-page-btn" data-page="' + (page - 1) + '"' + (page <= 1 ? ' disabled' : '') + '>Previous</button>');
        const visible = [];
        if (pages <= 7) {
            for (let i = 1; i <= pages; i += 1) visible.push(i);
        } else {
            [1, pages, page - 1, page, page + 1].forEach(function (n) {
                if (n >= 1 && n <= pages && visible.indexOf(n) === -1) visible.push(n);
            });
            visible.sort(function (a, b) { return a - b; });
        }
        let last = 0;
        visible.forEach(function (n) {
            if (last && n - last > 1) {
                buttons.push('<span class="lcat-page-gap">…</span>');
            }
            buttons.push('<button type="button" class="lcat-page-btn' + (n === page ? ' is-current' : '') + '" data-page="' + n + '"' + (n === page ? ' aria-current="page"' : '') + '>' + n + '</button>');
            last = n;
        });
        buttons.push('<button type="button" class="lcat-page-btn is-next" data-page="' + (page + 1) + '"' + (page >= pages ? ' disabled' : '') + '>Next</button>');
        pager.innerHTML = buttons.join('');
    }

    function renderPage() {
        const grid = document.getElementById('catalog-product-grid');
        const range = document.getElementById('lcat-range');
        const total = document.getElementById('lcat-total');
        const pages = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
        if (page > pages) page = pages;
        const start = filtered.length ? (page - 1) * PAGE_SIZE : 0;
        const slice = filtered.slice(start, start + PAGE_SIZE);
        const end = filtered.length ? start + slice.length : 0;

        if (range) range.textContent = filtered.length ? (start + 1) + '–' + end : '0';
        if (total) total.textContent = String(filtered.length);
        if (grid) {
            grid.innerHTML = slice.length
                ? slice.map(cardHtml).join('')
                : '<div class="lcat-empty">No products match these filters. Reset them or choose another department.</div>';
        }
        renderPager();
    }

    function applyCatalogFilters(options) {
        const resetPage = !options || options.resetPage !== false;
        const selected = checkedValues('lcat-type');
        let minPrice = readPrice('lcat-price-min', priceFloor);
        let maxPrice = readPrice('lcat-price-max', priceCeil);
        if (minPrice > maxPrice) {
            const swap = minPrice;
            minPrice = maxPrice;
            maxPrice = swap;
        }
        const ingredientIds = checkedValues('lcat-ingredient');
        const ratings = checkedValues('lcat-rating').map(Number).filter(function (n) { return n > 0; });
        const minRating = ratings.length ? Math.min.apply(null, ratings) : 0;
        const inStockOnly = Boolean(document.getElementById('lcat-instock') && document.getElementById('lcat-instock').checked);
        const sort = (document.getElementById('lcat-sort') || {}).value || 'featured';

        filtered = source.filter(function (product) {
            if (selected.length && !selected.some(function (slug) { return productInCategory(product, slug); })) {
                return false;
            }
            const price = Number(product.price || 0);
            if (price < minPrice || price > maxPrice) return false;
            if (minRating && Number(product.rating || 0) < minRating) return false;
            if (inStockOnly && (product.inStock === false || product.inStock === 0 || product.inStock === '0')) return false;
            if (ingredientIds.length) {
                const text = productText(product);
                const hit = ingredientIds.some(function (id) {
                    const ingredient = ingredients.find(function (entry) { return entry.id === id; });
                    return ingredient ? ingredient.test.test(text) : false;
                });
                if (!hit) return false;
            }
            if (searchQuery) {
                return productText(product).toLowerCase().indexOf(searchQuery) !== -1;
            }
            return true;
        });

        if (sort === 'price-low') {
            filtered.sort(function (a, b) { return Number(a.price) - Number(b.price); });
        } else if (sort === 'price-high') {
            filtered.sort(function (a, b) { return Number(b.price) - Number(a.price); });
        } else if (sort === 'rating') {
            filtered.sort(function (a, b) { return Number(b.rating || 0) - Number(a.rating || 0); });
        } else if (sort === 'popularity') {
            filtered.sort(function (a, b) { return Number(b.reviews || 0) - Number(a.reviews || 0); });
        } else {
            filtered.sort(function (a, b) { return a._order - b._order; });
        }

        if (resetPage) page = 1;
        updateHero(selected);
        updateFilterCount(selected, minPrice, maxPrice, ingredientIds, minRating, inStockOnly);
        syncUrl(selected);
        syncHeaderCategory(selected);
        renderPage();
    }

    function setSort(value) {
        ['lcat-sort', 'lcat-sort-mobile'].forEach(function (id) {
            const select = document.getElementById(id);
            if (select) select.value = value;
        });
    }

    function closeMobileFilters() {
        const sidebar = document.getElementById('lcat-sidebar');
        const backdrop = document.getElementById('lcat-filter-backdrop');
        if (sidebar) sidebar.classList.remove('is-open');
        if (backdrop) backdrop.hidden = true;
        document.body.style.overflow = '';
    }

    function openMobileFilters() {
        const sidebar = document.getElementById('lcat-sidebar');
        const backdrop = document.getElementById('lcat-filter-backdrop');
        if (sidebar) sidebar.classList.add('is-open');
        if (backdrop) backdrop.hidden = false;
        document.body.style.overflow = 'hidden';
    }

    function toggleMobileFilters() {
        const sidebar = document.getElementById('lcat-sidebar');
        if (sidebar && sidebar.classList.contains('is-open')) {
            closeMobileFilters();
        } else {
            openMobileFilters();
        }
    }

    function resetFilters() {
        document.querySelectorAll('#lcat-sidebar input[type="checkbox"]').forEach(function (input) {
            input.checked = false;
        });
        const min = document.getElementById('lcat-price-min');
        const max = document.getElementById('lcat-price-max');
        if (min) min.value = String(priceFloor);
        if (max) max.value = String(priceCeil);
        setSort('featured');
        applyCatalogFilters();
        if (typeof window.showToast === 'function') {
            window.showToast('All filters have been reset.');
        }
    }

    function buildIngredients() {
        const list = document.getElementById('lcat-ingredient-list');
        const block = document.getElementById('lcat-ingredients');
        if (!list || !block) return;
        const available = ingredients.map(function (ingredient) {
            const count = source.filter(function (product) {
                return ingredient.test.test(productText(product));
            }).length;
            return Object.assign({}, ingredient, { count: count });
        }).filter(function (ingredient) { return ingredient.count > 0; });

        if (!available.length) {
            block.hidden = true;
            return;
        }
        block.hidden = false;
        list.innerHTML = available.map(function (ingredient) {
            return '<label class="lcat-check"><span class="lcat-check-label"><input type="checkbox" name="lcat-ingredient" value="' + ingredient.id + '"> ' + esc(ingredient.label) + '</span><span class="lcat-count">(' + ingredient.count + ')</span></label>';
        }).join('');
    }

    function renderSearchDropdown(query) {
        const box = document.getElementById('search-results');
        const clearBtn = document.getElementById('clear-search');
        if (clearBtn) clearBtn.classList.toggle('hidden', !query);
        if (!box) return;
        if (!query) {
            box.classList.add('hidden');
            box.innerHTML = '';
            return;
        }
        const matches = source.filter(function (product) {
            return productText(product).toLowerCase().indexOf(query) !== -1;
        }).slice(0, 6);
        box.classList.remove('hidden');
        box.innerHTML = matches.length
            ? matches.map(function (product) {
                return '<a href="/products/' + encodeURIComponent(product.slug || '') + '" class="flex items-center gap-3 p-2 rounded-xl hover:bg-purple-50 transition">'
                    + '<img src="' + esc(product.image || placeholder) + '" alt="" class="w-10 h-10 object-cover rounded-lg">'
                    + '<span><span class="block text-xs font-bold text-slate-800">' + esc(product.name) + '</span>'
                    + '<span class="block text-[10px] text-slate-500">' + esc(money(product.price)) + ' · ' + esc(product.category || '') + '</span></span></a>';
            }).join('')
            : '<div class="p-3 text-xs text-slate-500 text-center">No products found for "' + esc(query) + '".</div>';
    }

    function setSearch(value, sourceId) {
        searchQuery = String(value || '').trim().toLowerCase();
        ['main-search-input', 'mobile-search-input'].forEach(function (id) {
            if (id === sourceId) return;
            const input = document.getElementById(id);
            if (input && input.value !== value) input.value = value;
        });
        renderSearchDropdown(searchQuery);
        applyCatalogFilters();
    }

    function updateWishlistUI() {
        ['wishlist-badge', 'wishlist-badge-desktop', 'mobile-wishlist-badge'].forEach(function (id) {
            const badge = document.getElementById(id);
            if (badge) badge.textContent = String(wishlist.length);
        });
        const container = document.getElementById('wishlist-items');
        if (!container) return;
        if (!wishlist.length) {
            container.innerHTML = '<div class="text-center py-12 text-slate-400 text-xs">No saved items in your wishlist yet.</div>';
            return;
        }
        container.innerHTML = wishlist.map(function (item) {
            const id = Number(item.id);
            return '<div class="flex items-center gap-4 p-3 rounded-2xl bg-purple-50/50 border border-purple-100">'
                + '<img src="' + esc(item.image || placeholder) + '" alt="" class="w-16 h-16 object-cover rounded-xl">'
                + '<div class="flex-1"><h4 class="text-xs font-bold text-beautage-purple">' + esc(item.name) + '</h4>'
                + '<span class="text-xs font-extrabold text-slate-800">' + esc(money(item.price)) + '</span></div>'
                + '<div class="flex flex-col gap-2"><button type="button" class="px-3 py-1.5 bg-beautage-pink text-white rounded-lg text-[10px] font-bold" data-wish-cart="' + id + '">Move to Bag</button>'
                + '<button type="button" class="text-rose-500 text-[10px] font-semibold text-center" data-wish="' + id + '">Remove</button></div></div>';
        }).join('');
    }

    function toggleWishlist(productId) {
        const id = Number(productId);
        const product = source.find(function (item) { return Number(item.id) === id; });
        if (!product) return;
        const index = wishlist.findIndex(function (item) { return Number(item.id) === id; });
        if (index > -1) {
            wishlist.splice(index, 1);
            if (typeof window.showToast === 'function') window.showToast('Removed "' + product.name + '" from your wishlist.');
        } else {
            wishlist.push(product);
            if (typeof window.showToast === 'function') window.showToast('Saved "' + product.name + '" to your wishlist.');
        }
        updateWishlistUI();
        renderPage();
    }

    function presetCategories() {
        const raw = String(config.activeSlug || '');
        const preset = raw.split(',').map(function (slug) { return slug.trim(); }).filter(Boolean);
        if (!preset.length) return;
        document.querySelectorAll('input[name="lcat-type"]').forEach(function (input) {
            input.checked = preset.indexOf(input.value) !== -1;
        });
    }

    function init() {
        buildIngredients();
        presetCategories();
        const sidebar = document.getElementById('lcat-sidebar');
        if (sidebar) {
            sidebar.addEventListener('change', function (event) {
                if (event.target && event.target.matches('input')) applyCatalogFilters();
            });
        }
        let priceTimer = null;
        ['lcat-price-min', 'lcat-price-max'].forEach(function (id) {
            const input = document.getElementById(id);
            if (!input) return;
            input.addEventListener('input', function () {
                window.clearTimeout(priceTimer);
                priceTimer = window.setTimeout(function () { applyCatalogFilters(); }, 250);
            });
        });
        ['lcat-sort', 'lcat-sort-mobile'].forEach(function (id) {
            const select = document.getElementById(id);
            if (!select) return;
            select.addEventListener('change', function () {
                setSort(select.value);
                applyCatalogFilters();
            });
        });
        const apply = document.getElementById('lcat-apply');
        if (apply) {
            apply.addEventListener('click', function () {
                applyCatalogFilters();
                closeMobileFilters();
                if (typeof window.showToast === 'function') window.showToast('Filters applied.');
            });
        }
        const reset = document.getElementById('lcat-reset');
        if (reset) reset.addEventListener('click', resetFilters);
        const toggle = document.getElementById('lcat-filter-toggle');
        if (toggle) toggle.addEventListener('click', toggleMobileFilters);
        const close = document.getElementById('lcat-filter-close');
        if (close) close.addEventListener('click', closeMobileFilters);
        const backdrop = document.getElementById('lcat-filter-backdrop');
        if (backdrop) backdrop.addEventListener('click', closeMobileFilters);
        window.addEventListener('resize', function () {
            if (window.innerWidth >= 1024) closeMobileFilters();
        });

        const grid = document.getElementById('catalog-product-grid');
        if (grid) {
            grid.addEventListener('click', function (event) {
                const wish = event.target.closest('[data-wish]');
                const add = event.target.closest('[data-add]');
                if (wish) {
                    event.preventDefault();
                    toggleWishlist(wish.getAttribute('data-wish'));
                } else if (add && !add.disabled && typeof window.addToCart === 'function') {
                    window.addToCart(Number(add.getAttribute('data-add')));
                }
            });
        }
        const pages = document.getElementById('lcat-pages');
        if (pages) {
            pages.addEventListener('click', function (event) {
                const button = event.target.closest('[data-page]');
                if (!button || button.disabled) return;
                const next = Number(button.getAttribute('data-page'));
                const last = Math.max(1, Math.ceil(filtered.length / PAGE_SIZE));
                page = Math.min(last, Math.max(1, next));
                renderPage();
                const toolbar = document.querySelector('.lcat-toolbar') || document.getElementById('catalog-product-grid');
                if (toolbar) toolbar.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
        const wishlistBox = document.getElementById('wishlist-items');
        if (wishlistBox) {
            wishlistBox.addEventListener('click', function (event) {
                const cartBtn = event.target.closest('[data-wish-cart]');
                const wish = event.target.closest('[data-wish]');
                if (cartBtn && typeof window.addToCart === 'function') {
                    const id = Number(cartBtn.getAttribute('data-wish-cart'));
                    window.addToCart(id);
                    toggleWishlist(id);
                } else if (wish) {
                    toggleWishlist(wish.getAttribute('data-wish'));
                }
            });
        }

        ['main-search-input', 'mobile-search-input'].forEach(function (id) {
            const input = document.getElementById(id);
            if (!input) return;
            input.addEventListener('input', function () { setSearch(input.value, id); });
        });
        const clearBtn = document.getElementById('clear-search');
        if (clearBtn) {
            clearBtn.addEventListener('click', function () { setSearch('', 'clear-search'); });
        }
        const headerCategory = document.getElementById('search-category');
        if (headerCategory) {
            headerCategory.addEventListener('change', function () {
                const slug = headerCategory.value;
                document.querySelectorAll('input[name="lcat-type"]').forEach(function (input) {
                    input.checked = slug !== 'all' && input.value === slug;
                });
                applyCatalogFilters();
            });
        }

        applyCatalogFilters();
        updateWishlistUI();
    }

    window.applyCatalogFilters = applyCatalogFilters;
    window.resetCatalogFilters = resetFilters;
    window.toggleCatalogFilters = toggleMobileFilters;
    window.toggleWishlist = toggleWishlist;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
