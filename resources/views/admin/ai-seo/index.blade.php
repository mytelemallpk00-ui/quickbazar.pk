<x-filament-panels::page>
    @php
        $stats = $this->stats;
        $seoScore = $this->seoScore;
        $products = $this->products;
        $categories = $this->categories;
        $selectedCount = count($this->selectedIds);
        $optimizeUrl = url('/admin/ai-seo/optimize');
        $bulkUrl = url('/admin/ai-seo/optimize-bulk');
        $csrf = csrf_token();
        $batchSize = (int) config('services.ai_seo.bulk_batch_size', 10);
        $listView = $this->listViewMeta;
    @endphp

    <div class="beautage-ai-seo-module space-y-4" data-ai-seo-provider="{{ $this->provider }}">
        {{-- Overview: Smart SEO–style score + KPIs --}}
        <div class="beautage-ai-seo-overview">
            <div class="beautage-ai-seo-score-card">
                <div class="beautage-ai-seo-score-ring" style="--score: {{ $seoScore }};">
                    <span class="beautage-ai-seo-score-value">{{ $seoScore }}%</span>
                </div>
                <div>
                    <p class="beautage-ai-seo-score-title">Store SEO coverage</p>
                    <p class="beautage-ai-seo-score-desc">Share of products with AI-optimized meta titles, descriptions, and tags.</p>
                </div>
            </div>
            <div class="beautage-ai-seo-kpi-row" role="group" aria-label="Filter product list">
                <button type="button" wire:click="setListView('all')" @class([
                    'beautage-ai-seo-kpi',
                    'beautage-ai-seo-kpi--clickable',
                    'is-active' => $this->seoFilter === 'all',
                ]) title="Show all products">
                    <span class="beautage-ai-seo-kpi-num">{{ number_format($stats['total']) }}</span>
                    <span class="beautage-ai-seo-kpi-label">All products</span>
                </button>
                <button type="button" wire:click="setListView('optimized')" @class([
                    'beautage-ai-seo-kpi',
                    'beautage-ai-seo-kpi--ok',
                    'beautage-ai-seo-kpi--clickable',
                    'is-active' => $this->seoFilter === 'optimized',
                ]) title="View optimized products only">
                    <span class="beautage-ai-seo-kpi-num">{{ number_format($stats['optimized']) }}</span>
                    <span class="beautage-ai-seo-kpi-label">Optimized</span>
                    <span class="beautage-ai-seo-kpi-hint">View list</span>
                </button>
                <button type="button" wire:click="setListView('needs')" @class([
                    'beautage-ai-seo-kpi',
                    'beautage-ai-seo-kpi--warn',
                    'beautage-ai-seo-kpi--clickable',
                    'is-active' => $this->seoFilter === 'needs',
                ]) title="View products that need SEO">
                    <span class="beautage-ai-seo-kpi-num">{{ number_format($stats['unoptimized']) }}</span>
                    <span class="beautage-ai-seo-kpi-label">To optimize</span>
                    <span class="beautage-ai-seo-kpi-hint">View list</span>
                </button>
                @if($selectedCount > 0)
                <div class="beautage-ai-seo-kpi beautage-ai-seo-kpi--accent">
                    <span class="beautage-ai-seo-kpi-num">{{ number_format($selectedCount) }}</span>
                    <span class="beautage-ai-seo-kpi-label">Selected</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Settings: generation model (no dropdown icons) --}}
        <div class="beautage-ai-seo-panel">
            <div class="beautage-ai-seo-panel-head">
                <div>
                    <h3 class="beautage-ai-seo-panel-title">AI generation model</h3>
                    <p class="beautage-ai-seo-panel-sub">Bulk meta tags, descriptions, and slugs — same engines as product edit.</p>
                </div>
                <a href="{{ route('filament.admin.pages.ai-integration') }}" class="beautage-ai-seo-text-link">Manage API keys</a>
            </div>
            <div class="beautage-ai-seo-segments" role="radiogroup" aria-label="AI provider">
                <button type="button"
                    wire:click="setProviderOption('smart')"
                    @class(['beautage-ai-seo-segment', 'is-active' => $this->provider === 'smart'])
                    data-provider="smart">
                    Smart SEO
                    <span class="beautage-ai-seo-segment-hint">No key</span>
                </button>
                <button type="button"
                    wire:click="setProviderOption('openai')"
                    @class(['beautage-ai-seo-segment', 'is-active' => $this->provider === 'openai'])
                    data-provider="openai">
                    OpenAI
                </button>
                <button type="button"
                    wire:click="setProviderOption('gemini')"
                    @class(['beautage-ai-seo-segment', 'is-active' => $this->provider === 'gemini'])
                    data-provider="gemini">
                    Gemini Pro
                </button>
            </div>
        </div>

        {{-- Categories --}}
        <div class="beautage-ai-seo-panel">
            <div class="beautage-ai-seo-panel-head">
                <h3 class="beautage-ai-seo-panel-title">Optimize by category</h3>
                @if($this->categoryFilter !== '')
                    <button type="button" wire:click="$set('categoryFilter', '')" class="beautage-ai-seo-text-link">Clear category</button>
                @endif
            </div>
            <div class="beautage-ai-seo-category-scroll">
                @foreach($categories as $cat)
                <div @class([
                    'beautage-ai-seo-cat-chip',
                    'is-active' => (string) $this->categoryFilter === (string) $cat->id,
                ])>
                    <button type="button" wire:click="filterByCategory('{{ $cat->id }}')" class="beautage-ai-seo-cat-chip-main">
                        <span class="beautage-ai-seo-cat-name">{{ $cat->name }}</span>
                        <span class="beautage-ai-seo-cat-count">{{ $cat->unoptimized_count }} pending · {{ $cat->products_count }} total</span>
                    </button>
                    <button type="button"
                        class="beautage-ai-seo-cat-run"
                        data-category-bulk="{{ $cat->id }}"
                        data-category-name="{{ $cat->name }}">
                        Run AI
                    </button>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Product list filters --}}
        <div class="beautage-ai-seo-panel beautage-ai-seo-panel--compact">
            <div class="beautage-ai-seo-filter-bar">
                <div class="beautage-ai-seo-field">
                    <label class="beautage-ai-seo-field-label" for="ai-seo-category">Collection</label>
                    <div class="beautage-ai-seo-select-shell">
                        <select id="ai-seo-category" wire:model.change="categoryFilter" class="beautage-ai-seo-select">
                            <option value="">All collections</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="beautage-ai-seo-field beautage-ai-seo-field--grow">
                    <label class="beautage-ai-seo-field-label" for="ai-seo-search">Search products</label>
                    <input id="ai-seo-search" type="text" wire:model.live.debounce.400ms="search" placeholder="Name, slug, or meta title" class="beautage-ai-seo-input" autocomplete="off">
                </div>
                <div class="beautage-ai-seo-field">
                    <span class="beautage-ai-seo-field-label">Status</span>
                    <div class="beautage-ai-seo-segments beautage-ai-seo-segments--sm" role="tablist">
                        <button type="button" wire:click="setListView('all')" @class(['beautage-ai-seo-segment', 'beautage-ai-seo-segment--sm', 'is-active' => $this->seoFilter === 'all'])>All</button>
                        <button type="button" wire:click="setListView('needs')" @class(['beautage-ai-seo-segment', 'beautage-ai-seo-segment--sm', 'is-active' => $this->seoFilter === 'needs'])>To optimize</button>
                        <button type="button" wire:click="setListView('optimized')" @class(['beautage-ai-seo-segment', 'beautage-ai-seo-segment--sm', 'is-active' => $this->seoFilter === 'optimized'])>Optimized</button>
                    </div>
                </div>
            </div>
        </div>

        <div id="ai-seo-product-list" @class([
            'beautage-ai-seo-list-header',
            'beautage-ai-seo-list-header--'.$listView['tone'],
        ])>
            <div>
                <h3 class="beautage-ai-seo-list-title">{{ $listView['title'] }}</h3>
                <p class="beautage-ai-seo-list-desc">{{ $listView['description'] }}</p>
            </div>
            <div class="beautage-ai-seo-list-meta">
                <span class="beautage-ai-seo-list-count">{{ number_format($listView['count']) }} in this view</span>
                @if($this->seoFilter !== 'all')
                    <button type="button" wire:click="setListView('all')" class="beautage-ai-seo-text-link">Show all products</button>
                @endif
            </div>
        </div>

        @if($selectedCount > 0)
        <div class="beautage-ai-seo-selection-bar">
            <span class="beautage-ai-seo-selection-text"><strong>{{ $selectedCount }}</strong> selected</span>
            <div class="beautage-ai-seo-selection-actions">
                <label class="beautage-ai-seo-check-inline">
                    <input type="checkbox" id="ai-seo-only-unoptimized" checked>
                    Skip optimized
                </label>
                <button type="button" id="ai-seo-selected-bulk-btn" class="beautage-ai-seo-btn beautage-ai-seo-btn--primary">Optimize selected</button>
                <button type="button" wire:click="clearSelection" class="beautage-ai-seo-btn beautage-ai-seo-btn--outline">Clear</button>
            </div>
        </div>
        @endif

        <div class="beautage-ai-seo-table-toolbar">
            <div class="beautage-ai-seo-table-toolbar-left">
                <button type="button" wire:click="togglePageSelection" class="beautage-ai-seo-text-link">{{ $this->isPageFullySelected ? 'Deselect page' : 'Select page' }}</button>
                @if($this->seoFilter !== 'optimized')
                    <button type="button" wire:click="selectUnoptimizedOnPage" class="beautage-ai-seo-text-link">Select pending on page</button>
                    <button type="button" wire:click="selectAllUnoptimizedInView" class="beautage-ai-seo-text-link">Select all pending in view</button>
                @endif
            </div>
            <div class="beautage-ai-seo-table-toolbar-right">
                @if($this->seoFilter !== 'optimized')
                    <label class="beautage-ai-seo-check-inline">
                        <input type="checkbox" id="ai-seo-batch-only-unoptimized" checked>
                        Batch: pending only
                    </label>
                    <button type="button" id="ai-seo-bulk-btn" class="beautage-ai-seo-btn beautage-ai-seo-btn--dark">Next batch ({{ $batchSize }})</button>
                @else
                    <span class="beautage-ai-seo-toolbar-note">Re-generate individual products from the table.</span>
                @endif
            </div>
        </div>

        <div id="ai-seo-progress" class="hidden beautage-ai-seo-progress" role="status">
            <div class="beautage-ai-seo-progress-bar"><span id="ai-seo-progress-fill"></span></div>
            <p id="ai-seo-progress-text" class="beautage-ai-seo-progress-text">Working…</p>
        </div>
        <div id="ai-seo-toast" class="hidden beautage-ai-seo-toast" role="status"></div>

        <div class="beautage-ai-seo-table-wrap">
            <table class="beautage-ai-seo-table">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" class="beautage-ai-seo-checkbox" @checked($this->isPageFullySelected) wire:click="togglePageSelection" aria-label="Select page">
                        </th>
                        <th>Product</th>
                        <th>Collection</th>
                        <th>URL handle</th>
                        <th>Meta</th>
                        <th class="text-right"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr wire:key="ai-seo-product-{{ $product->id }}" @class(['beautage-ai-seo-row--selected' => in_array($product->id, $this->selectedIds, true)])>
                        <td>
                            <input type="checkbox" class="beautage-ai-seo-checkbox beautage-ai-seo-row-check" wire:model.live="selectedIds" value="{{ $product->id }}" aria-label="Select {{ $product->name }}">
                        </td>
                        <td>
                            <div class="beautage-ai-seo-product-name">{{ $product->name }}</div>
                            <div class="beautage-ai-seo-product-meta">{{ $product->seo_title ?: $product->meta_title ?: '—' }}</div>
                        </td>
                        <td><span class="beautage-ai-seo-pill-tag">{{ $product->productCategory?->name ?? $product->category ?? '—' }}</span></td>
                        <td class="beautage-ai-seo-mono">{{ \Illuminate\Support\Str::limit($product->slug, 32) }}</td>
                        <td>
                            @if($product->is_seo_optimized)
                                <span class="beautage-ai-seo-status beautage-ai-seo-status--ok">Optimized</span>
                            @else
                                <span class="beautage-ai-seo-status beautage-ai-seo-status--pending">Pending</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <button type="button" class="beautage-ai-seo-link-action" data-optimize-id="{{ $product->id }}">{{ $product->is_seo_optimized ? 'Re-generate' : 'Generate' }}</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="beautage-ai-seo-empty">
                            @if($this->seoFilter === 'needs')
                                @if($stats['unoptimized'] === 0)
                                    All products are optimized. Great job!
                                @else
                                    No pending products match your search or collection filter.
                                @endif
                            @elseif($this->seoFilter === 'optimized')
                                @if($stats['optimized'] === 0)
                                    No optimized products yet. Open <button type="button" wire:click="setListView('needs')" class="beautage-ai-seo-text-link">To optimize</button> to get started.
                                @else
                                    No optimized products match your search or collection filter.
                                @endif
                            @else
                                No products match these filters.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="beautage-ai-seo-pagination">{{ $products->links() }}</div>
    </div>

    @script
    <script>
        Livewire.on('ai-seo-scroll-to-list', () => {
            document.getElementById('ai-seo-product-list')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });

        const toast = document.getElementById('ai-seo-toast');
        const progress = document.getElementById('ai-seo-progress');
        const progressFill = document.getElementById('ai-seo-progress-fill');
        const progressText = document.getElementById('ai-seo-progress-text');
        const selectedIds = @js($this->selectedIds);

        function provider() {
            const active = document.querySelector('.beautage-ai-seo-segments[aria-label="AI provider"] .beautage-ai-seo-segment.is-active');
            return active?.dataset.provider || document.querySelector('.beautage-ai-seo-module')?.dataset.aiSeoProvider || 'smart';
        }

        function showToast(msg, ok = true) {
            if (!toast) return;
            toast.textContent = msg;
            toast.classList.remove('hidden', 'beautage-ai-seo-toast--error', 'beautage-ai-seo-toast--ok');
            toast.classList.add(ok ? 'beautage-ai-seo-toast--ok' : 'beautage-ai-seo-toast--error');
            setTimeout(() => toast.classList.add('hidden'), 6000);
        }

        function setProgress(visible, pct = 0, text = '') {
            if (!progress) return;
            progress.classList.toggle('hidden', !visible);
            if (progressFill) progressFill.style.width = `${Math.min(100, pct)}%`;
            if (progressText && text) progressText.textContent = text;
        }

        async function postJson(url, body = {}) {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': @js($csrf),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify(body),
            });
            const text = await res.text();
            try {
                const data = JSON.parse(text);
                if (!res.ok && data.message) {
                    return { success: false, message: data.message };
                }
                return data;
            } catch {
                return {
                    success: false,
                    message: res.ok ? 'Invalid server response.' : (text.includes('Server Error') ? 'Server error — try again or contact support.' : `Request failed (${res.status}).`),
                };
            }
        }

        function liveSelectedIds() {
            return Array.from(document.querySelectorAll('.beautage-ai-seo-row-check:checked'))
                .map((el) => parseInt(el.value, 10))
                .filter((id) => !Number.isNaN(id));
        }

        async function runBulkLoop(buildBody, label) {
            let totalOk = 0;
            let totalFail = 0;
            let rounds = 0;
            setProgress(true, 5, label);

            while (rounds < 200) {
                rounds++;
                const body = typeof buildBody === 'function' ? buildBody(rounds) : buildBody;
                const result = await postJson(@js($bulkUrl), body);
                if (!result.success) {
                    setProgress(false);
                    showToast(result.message || 'Bulk failed', false);
                    return;
                }
                totalOk += result.processed || 0;
                totalFail += result.failed || 0;
                const done = result.processed === 0 && result.failed === 0;
                setProgress(true, Math.min(95, rounds * 8), `${label} — ${totalOk} done`);
                if (done || body.singleBatch) break;
                await new Promise((r) => setTimeout(r, 400));
            }

            setProgress(true, 100, 'Complete');
            setTimeout(() => setProgress(false), 800);
            showToast(`Finished: ${totalOk} optimized${totalFail ? `, ${totalFail} failed` : ''}.`, totalFail === 0);
            window.location.reload();
        }

        document.getElementById('ai-seo-bulk-btn')?.addEventListener('click', async () => {
            const onlyUnoptimized = document.getElementById('ai-seo-batch-only-unoptimized')?.checked ?? true;
            const categoryId = @js($this->categoryFilter !== '' ? (int) $this->categoryFilter : null);
            await runBulkLoop(() => ({
                provider: provider(),
                only_unoptimized: onlyUnoptimized,
                category_id: categoryId,
                singleBatch: true,
            }), 'Running batch');
        });

        document.getElementById('ai-seo-selected-bulk-btn')?.addEventListener('click', async () => {
            let ids = liveSelectedIds();
            if (!ids.length) ids = selectedIds;
            if (!ids.length) {
                showToast('Select at least one product.', false);
                return;
            }
            const onlyUnoptimized = document.getElementById('ai-seo-only-unoptimized')?.checked ?? true;
            const batchSize = @js($batchSize);
            let remaining = [...ids];
            let totalOk = 0;
            let totalFail = 0;
            setProgress(true, 0, 'Optimizing selection…');

            while (remaining.length) {
                const chunk = remaining.slice(0, batchSize);
                remaining = remaining.slice(batchSize);
                const result = await postJson(@js($bulkUrl), {
                    provider: provider(),
                    product_ids: chunk,
                    only_unoptimized: onlyUnoptimized,
                });
                if (!result.success) {
                    setProgress(false);
                    showToast(result.message || 'Failed', false);
                    return;
                }
                totalOk += result.processed || 0;
                totalFail += result.failed || 0;
                setProgress(true, Math.round(((ids.length - remaining.length) / ids.length) * 100), `${totalOk} of ${ids.length}`);
            }

            setProgress(true, 100, 'Complete');
            setTimeout(() => setProgress(false), 800);
            showToast(`Selected: ${totalOk} optimized.`, totalFail === 0);
            window.location.reload();
        });

        document.querySelectorAll('[data-category-bulk]').forEach((btn) => {
            btn.addEventListener('click', async (e) => {
                e.stopPropagation();
                const categoryId = btn.getAttribute('data-category-bulk');
                const name = btn.getAttribute('data-category-name') || 'category';
                if (!confirm(`Run AI SEO for all pending products in “${name}”?`)) return;
                await runBulkLoop(() => ({
                    provider: provider(),
                    only_unoptimized: true,
                    category_id: parseInt(categoryId, 10),
                }), name);
            });
        });

        document.querySelector('.beautage-ai-seo-table-wrap')?.addEventListener('click', async (event) => {
            const btn = event.target.closest('[data-optimize-id]');
            if (!btn || btn.disabled) return;
            const id = btn.getAttribute('data-optimize-id');
            btn.disabled = true;
            const prev = btn.textContent;
            btn.textContent = '…';
            try {
                const result = await postJson(@js($optimizeUrl) + '/' + id, { provider: provider() });
                if (result.success) {
                    showToast('Meta tags generated.', true);
                    window.location.reload();
                } else {
                    showToast(result.message || 'Failed', false);
                    btn.disabled = false;
                    btn.textContent = prev;
                }
            } catch {
                showToast('Request failed', false);
                btn.disabled = false;
                btn.textContent = prev;
            }
        });
    </script>
    @endscript
</x-filament-panels::page>
