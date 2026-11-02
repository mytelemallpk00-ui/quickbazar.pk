<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Models\Category;
use App\Models\Product;
use App\Services\AiSeo\AiSeoManager;
use App\Support\AdminPermissions;
use App\Support\AiSettings;
use Filament\Pages\Page;
use Livewire\WithPagination;

class AiSeoOptimizer extends Page
{
    use AuthorizesAdminPage;
    use WithPagination;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CATALOG;
    }

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?string $navigationLabel = 'AI SEO Optimizer';

    protected static ?string $navigationGroup = 'AI SEO';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'AI SEO Optimizer';

    protected static ?string $slug = 'ai-seo';

    protected static string $view = 'admin.ai-seo.index';

    public string $provider = 'smart';

    public string $search = '';

    /** @var ''|numeric-string */
    public string $categoryFilter = '';

    /** all|needs|optimized */
    public string $seoFilter = 'all';

    /** @var list<int> */
    public array $selectedIds = [];

    /** @var array<string, string|list<int>> */
    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'seoFilter' => ['except' => 'all'],
    ];

    public function mount(): void
    {
        $this->provider = AiSettings::seoProvider();
    }

    public function updatedProvider(string $value): void
    {
        AiSettings::setSeoProvider($value);
    }

    public function setProviderOption(string $provider): void
    {
        $this->provider = $provider;
        AiSettings::setSeoProvider($provider);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
        $this->selectedIds = [];
    }

    public function updatedSeoFilter(): void
    {
        $this->resetPage();
    }

    public function setListView(string $view): void
    {
        if (! in_array($view, ['all', 'needs', 'optimized'], true)) {
            return;
        }

        $this->seoFilter = $view;
        $this->resetPage();
        $this->dispatch('ai-seo-scroll-to-list');
    }

    /** @return array{title: string, description: string, count: int, tone: string} */
    public function getListViewMetaProperty(): array
    {
        $stats = $this->stats;

        return match ($this->seoFilter) {
            'needs' => [
                'title' => 'To optimize',
                'description' => 'These products still need AI meta titles, descriptions, and tags.',
                'count' => $stats['unoptimized'],
                'tone' => 'warn',
            ],
            'optimized' => [
                'title' => 'Optimized',
                'description' => 'These products already have AI SEO applied. Re-generate if you changed copy or keywords.',
                'count' => $stats['optimized'],
                'tone' => 'ok',
            ],
            default => [
                'title' => 'All products',
                'description' => 'Browse the full catalog or tap Optimized / To optimize above for a focused list.',
                'count' => $stats['total'],
                'tone' => 'neutral',
            ],
        };
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
    }

    public function filterByCategory(string $categoryId): void
    {
        $this->categoryFilter = $categoryId;
        $this->seoFilter = 'needs';
        $this->resetPage();
        $this->selectedIds = [];
    }

    public function togglePageSelection(): void
    {
        $pageIds = $this->products->getCollection()->pluck('id')->map(fn ($id): int => (int) $id)->all();
        if ($pageIds === []) {
            return;
        }

        $allOnPageSelected = collect($pageIds)->every(fn (int $id): bool => in_array($id, $this->selectedIds, true));

        if ($allOnPageSelected) {
            $this->selectedIds = array_values(array_diff($this->selectedIds, $pageIds));
        } else {
            $this->selectedIds = array_values(array_unique([...$this->selectedIds, ...$pageIds]));
        }
    }

    public function selectUnoptimizedOnPage(): void
    {
        $ids = $this->products->getCollection()
            ->filter(fn (Product $p): bool => ! $p->is_seo_optimized)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $this->selectedIds = array_values(array_unique([...$this->selectedIds, ...$ids]));
    }

    public function selectAllUnoptimizedInView(): void
    {
        $ids = $this->filteredProductQuery()
            ->where(function ($q): void {
                $q->where('is_seo_optimized', false)->orWhereNull('is_seo_optimized');
            })
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->all();

        $this->selectedIds = array_values(array_unique([...$this->selectedIds, ...$ids]));
    }

    public function getIsPageFullySelectedProperty(): bool
    {
        $pageIds = $this->products->getCollection()->pluck('id')->map(fn ($id): int => (int) $id)->all();
        if ($pageIds === []) {
            return false;
        }

        return collect($pageIds)->every(fn (int $id): bool => in_array($id, $this->selectedIds, true));
    }

    /** @return array<string, int> */
    public function getStatsProperty(): array
    {
        $categoryId = $this->categoryFilter !== '' ? (int) $this->categoryFilter : null;

        return app(AiSeoManager::class)->stats($categoryId);
    }

    public function getSeoScoreProperty(): int
    {
        $stats = $this->stats;
        if ($stats['total'] <= 0) {
            return 0;
        }

        return (int) round(($stats['optimized'] / $stats['total']) * 100);
    }

    public function getProductsProperty()
    {
        return $this->filteredProductQuery()
            ->with('productCategory')
            ->orderByDesc('updated_at')
            ->paginate(15);
    }

    public function getCategoriesProperty()
    {
        return Category::query()
            ->whereHas('products')
            ->withCount([
                'products',
                'products as unoptimized_count' => function ($q): void {
                    $q->where(function ($inner): void {
                        $inner->where('is_seo_optimized', false)->orWhereNull('is_seo_optimized');
                    });
                },
            ])
            ->orderBy('name')
            ->get();
    }

    protected function filteredProductQuery()
    {
        return Product::query()
            ->when($this->categoryFilter !== '', function ($q): void {
                $q->where('category_id', (int) $this->categoryFilter);
            })
            ->when($this->seoFilter === 'needs', function ($q): void {
                $q->where(function ($inner): void {
                    $inner->where('is_seo_optimized', false)->orWhereNull('is_seo_optimized');
                });
            })
            ->when($this->seoFilter === 'optimized', function ($q): void {
                $q->where('is_seo_optimized', true);
            })
            ->when($this->search !== '', function ($q): void {
                $term = '%'.$this->search.'%';
                $q->where(function ($inner) use ($term): void {
                    $inner->where('name', 'like', $term)
                        ->orWhere('slug', 'like', $term)
                        ->orWhere('seo_title', 'like', $term)
                        ->orWhere('category', 'like', $term);
                });
            });
    }
}
