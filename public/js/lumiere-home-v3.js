(function () {
    function showToast(message) {
        const container = document.getElementById('toast-container');
        if (!container) {
            return;
        }
        const toast = document.createElement('div');
        toast.className = 'lv3-toast';
        toast.textContent = String(message ?? '');
        container.appendChild(toast);
        window.requestAnimationFrame(function () {
            toast.classList.add('is-in');
        });
        window.setTimeout(function () {
            toast.classList.remove('is-in');
            window.setTimeout(function () {
                toast.remove();
            }, 280);
        }, 2800);
    }

    window.showToast = showToast;

    function esc(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    const wishKey = 'qb-wishlist-v1';
    let wishlist = [];

    function readWishlist() {
        try {
            const saved = JSON.parse(window.localStorage.getItem(wishKey) || '[]');
            wishlist = Array.isArray(saved) ? saved.filter(function (item) { return item && item.id; }) : [];
        } catch (error) {
            wishlist = [];
        }
    }

    function writeWishlist() {
        window.localStorage.setItem(wishKey, JSON.stringify(wishlist));
    }

    function paintHearts() {
        document.querySelectorAll('[data-lv3-wish]').forEach(function (button) {
            const id = String(button.getAttribute('data-id'));
            const saved = wishlist.some(function (item) { return String(item.id) === id; });
            button.classList.toggle('is-saved', saved);
            button.setAttribute('aria-pressed', saved ? 'true' : 'false');
        });
    }

    function paintWishlist() {
        ['wishlist-badge', 'wishlist-badge-desktop', 'mobile-wishlist-badge'].forEach(function (id) {
            const badge = document.getElementById(id);
            if (badge) {
                badge.textContent = String(wishlist.length);
            }
        });
        const box = document.getElementById('wishlist-items');
        if (!box) {
            return;
        }
        if (!wishlist.length) {
            box.innerHTML = '<div class="text-center py-12 text-slate-400 text-xs">No saved items in your wishlist yet.</div>';
            return;
        }
        box.innerHTML = wishlist.map(function (item) {
            return '<div class="flex items-center gap-4 p-3 rounded-2xl border border-slate-200">'
                + '<img src="' + esc(item.image) + '" alt="" class="w-16 h-16 object-cover rounded-xl">'
                + '<div class="flex-1"><h4 class="text-xs font-bold text-slate-900">' + esc(item.name) + '</h4>'
                + '<span class="text-xs font-extrabold text-slate-800">' + esc(item.price) + '</span></div>'
                + '<div class="flex flex-col gap-2">'
                + '<button type="button" class="px-3 py-1.5 rounded-lg text-[10px] font-bold" style="background:#0b132b;color:#d4af37" data-card-add data-id="' + esc(item.id) + '" data-name="' + esc(item.name) + '" data-price="' + esc(item.rawPrice || 0) + '" data-image="' + esc(item.image) + '">Move to Bag</button>'
                + '<button type="button" class="text-rose-500 text-[10px] font-semibold" data-lv3-remove="' + esc(item.id) + '">Remove</button>'
                + '</div></div>';
        }).join('');
    }

    function cardFrom(button) {
        return {
            id: button.getAttribute('data-id'),
            name: button.getAttribute('data-name') || 'Product',
            price: button.getAttribute('data-price') || '',
            rawPrice: button.getAttribute('data-raw-price') || button.getAttribute('data-price') || 0,
            image: button.getAttribute('data-image') || '',
            url: button.getAttribute('data-url') || '/collections',
        };
    }

    document.addEventListener('click', function (event) {
        const wish = event.target.closest('[data-lv3-wish]');
        if (wish) {
            event.preventDefault();
            event.stopPropagation();
            const item = cardFrom(wish);
            const index = wishlist.findIndex(function (row) { return String(row.id) === String(item.id); });
            if (index > -1) {
                wishlist.splice(index, 1);
                showToast('Removed "' + item.name + '" from your wishlist.');
            } else {
                wishlist.push(item);
                showToast('Saved "' + item.name + '" to your wishlist.');
            }
            writeWishlist();
            paintHearts();
            paintWishlist();
            return;
        }

        const remove = event.target.closest('[data-lv3-remove]');
        if (remove) {
            const id = String(remove.getAttribute('data-lv3-remove'));
            wishlist = wishlist.filter(function (row) { return String(row.id) !== id; });
            writeWishlist();
            paintHearts();
            paintWishlist();
        }
    });

    function startTimer() {
        const timer = document.querySelector('.lv3-timer');
        const hours = document.getElementById('hours');
        const minutes = document.getElementById('minutes');
        const seconds = document.getElementById('seconds');
        if (!timer || !hours || !minutes || !seconds) {
            return;
        }
        const ends = new Date(timer.getAttribute('data-ends')).getTime();
        if (!Number.isFinite(ends)) {
            return;
        }
        const tick = function () {
            const remaining = Math.max(0, Math.floor((ends - Date.now()) / 1000));
            hours.textContent = String(Math.floor(remaining / 3600)).padStart(2, '0');
            minutes.textContent = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            seconds.textContent = String(remaining % 60).padStart(2, '0');
        };
        tick();
        window.setInterval(tick, 1000);
    }

    function readQuiz() {
        const node = document.getElementById('lv3-quiz-data');
        if (!node) {
            return [];
        }
        try {
            const data = JSON.parse(node.textContent || '[]');
            return Array.isArray(data) ? data : [];
        } catch (error) {
            return [];
        }
    }

    function matchProduct(goal, finish, pool) {
        const stocked = pool.filter(function (item) { return item.in_stock; });
        const list = stocked.length ? stocked : pool.slice();
        const textOf = function (item) {
            return (item.name + ' ' + item.category + ' ' + (item.short || '')).toLowerCase();
        };
        const hair = /hair|scalp|shampoo|oil|growth/;
        const skin = /skin|serum|face|body|cream|lip|care/;
        let picks = list.filter(function (item) {
            const text = textOf(item);
            return goal === 'hair' ? hair.test(text) : skin.test(text);
        });
        if (!picks.length) {
            picks = list.slice();
        }
        if (finish === 'repair') {
            const refined = picks.filter(function (item) {
                return /mask|repair|oil|hydra|moisture|dry|serum/.test(textOf(item));
            });
            if (refined.length) {
                picks = refined;
            }
        }
        return picks[0] || null;
    }

    function startQuiz() {
        const step1 = document.getElementById('lv3-quiz-step-1');
        const step2 = document.getElementById('lv3-quiz-step-2');
        const result = document.getElementById('lv3-quiz-result');
        if (!step1 || !step2 || !result) {
            return;
        }
        const pool = readQuiz();
        let goal = '';

        function show(panel) {
            [step1, step2, result].forEach(function (node) {
                node.hidden = node !== panel;
            });
        }

        document.querySelectorAll('[data-quiz-step]').forEach(function (button) {
            button.addEventListener('click', function () {
                const step = button.getAttribute('data-quiz-step');
                const key = button.getAttribute('data-quiz-key') || '';
                button.parentElement.querySelectorAll('button').forEach(function (peer) {
                    peer.classList.remove('is-picked');
                });
                button.classList.add('is-picked');
                if (step === '1') {
                    goal = key;
                    show(step2);
                    return;
                }
                const product = matchProduct(goal, key, pool);
                if (!product) {
                    show(step1);
                    return;
                }
                const image = document.getElementById('lv3-quiz-img');
                const name = document.getElementById('lv3-quiz-name');
                const blurb = document.getElementById('lv3-quiz-blurb');
                const price = document.getElementById('lv3-quiz-price');
                const link = document.getElementById('lv3-quiz-link');
                const add = document.getElementById('lv3-quiz-add');
                image.src = product.image;
                image.alt = product.name;
                name.textContent = product.name;
                blurb.textContent = product.short || 'This match comes from the live catalog.';
                price.innerHTML = '<strong>' + esc(product.price_label) + '</strong>'
                    + (product.old_price_label ? '<s>' + esc(product.old_price_label) + '</s>' : '');
                link.href = product.url;
                if (product.in_stock) {
                    add.hidden = false;
                    add.setAttribute('data-card-add', '');
                    add.setAttribute('data-id', product.id);
                    add.setAttribute('data-name', product.name);
                    add.setAttribute('data-price', product.price || 0);
                    add.setAttribute('data-image', product.image || '');
                    add.setAttribute('data-url', product.url || '');
                    add.setAttribute('data-stock', '1');
                } else {
                    add.hidden = true;
                    add.removeAttribute('data-card-add');
                }
                show(result);
            });
        });

        const reset = document.getElementById('lv3-quiz-reset');
        if (reset) {
            reset.addEventListener('click', function () {
                goal = '';
                document.querySelectorAll('[data-quiz-step]').forEach(function (button) {
                    button.classList.remove('is-picked');
                });
                show(step1);
            });
        }
    }

    readWishlist();
    paintHearts();
    paintWishlist();
    startTimer();
    startQuiz();

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
})();
