@php
    use App\Models\User;
    use App\Support\AdminPermissions;

    /** @var User|null $adminUser */
    $adminUser = auth()->user();
    $can = fn (string $permission): bool => $adminUser instanceof User && $adminUser->canAccessAdminModule($permission);
@endphp
<x-filament-widgets::widget class="beautage-dashboard-mgmt-widget col-span-full">
    <div class="beautage-dash-panel">
        <header class="beautage-dash-panel-head">
            <div>
                <h3 class="beautage-dash-panel-title">Management</h3>
                <p class="beautage-dash-panel-desc">Quick access to catalog, content, orders, and site settings.</p>
            </div>
        </header>

        <div class="beautage-dash-mgmt-grid">
            @if($can(AdminPermissions::CATALOG) || $can(AdminPermissions::CATEGORIES))
            <div class="beautage-dash-link-group beautage-dash-link-group--cell">
                <h4 class="beautage-dash-link-group-title">
                    <x-filament::icon icon="heroicon-o-shopping-bag" class="h-4 w-4" />
                    Catalog
                </h4>
                <div class="beautage-admin-quicklinks">
                    @if($can(AdminPermissions::CATALOG))
                    <a href="{{ route('filament.admin.resources.products.index') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-shopping-bag" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Products</span>
                        <span class="beautage-admin-quicklink-desc">Pricing, images, flash deals, SEO</span>
                    </a>
                    <a href="{{ route('filament.admin.pages.bulk-product-import') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-arrow-down-tray" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Bulk import</span>
                        <span class="beautage-admin-quicklink-desc">Daraz, Amazon, Shopify, Walmart</span>
                    </a>
                    @endif
                    @if($can(AdminPermissions::CATEGORIES))
                    <a href="{{ route('filament.admin.resources.categories.index') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-tag" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Categories</span>
                        <span class="beautage-admin-quicklink-desc">Nav, collections, meta</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif

            @if($can(AdminPermissions::CONTENT))
            <div class="beautage-dash-link-group beautage-dash-link-group--cell">
                <h4 class="beautage-dash-link-group-title">
                    <x-filament::icon icon="heroicon-o-document-text" class="h-4 w-4" />
                    Content
                </h4>
                <div class="beautage-admin-quicklinks">
                    <a href="{{ route('filament.admin.resources.cms-pages.index') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-document-text" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Pages</span>
                        <span class="beautage-admin-quicklink-desc">Help, size guide, careers</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.blog-posts.index') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-newspaper" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Blog</span>
                        <span class="beautage-admin-quicklink-desc">Articles & rich SEO</span>
                    </a>
                    <a href="{{ route('filament.admin.resources.career-openings.index') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-briefcase" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Careers</span>
                        <span class="beautage-admin-quicklink-desc">Job listings & apply links</span>
                    </a>
                    <a href="{{ route('filament.admin.pages.partner-press-releases') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-megaphone" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Press releases</span>
                        <span class="beautage-admin-quicklink-desc">Partner hub page & SEO</span>
                    </a>
                </div>
            </div>
            @endif

            @if($can(AdminPermissions::SETTINGS) || $adminUser?->canManageAdminUsers())
            <div class="beautage-dash-link-group beautage-dash-link-group--cell">
                <h4 class="beautage-dash-link-group-title">
                    <x-filament::icon icon="heroicon-o-cog-6-tooth" class="h-4 w-4" />
                    Settings &amp; admin
                </h4>
                <div class="beautage-admin-quicklinks">
                    @if($can(AdminPermissions::SETTINGS))
                    <a href="{{ route('filament.admin.pages.homepage-settings') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-home-modern" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Homepage banners</span>
                        <span class="beautage-admin-quicklink-desc">Hero, promo cards, category images</span>
                    </a>
                    <a href="{{ route('filament.admin.pages.theme-settings') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-paint-brush" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Theme & site</span>
                        <span class="beautage-admin-quicklink-desc">Logo, favicon, colors, SEO</span>
                    </a>
                    <a href="{{ route('filament.admin.pages.checkout-settings') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-shopping-cart" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Checkout settings</span>
                        <span class="beautage-admin-quicklink-desc">Delivery charges & payment methods</span>
                    </a>
                    <a href="{{ route('filament.admin.pages.ai-integration') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-cpu-chip" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">AI integration</span>
                        <span class="beautage-admin-quicklink-desc">Gemini & OpenAI keys for product SEO</span>
                    </a>
                    <a href="{{ route('filament.admin.pages.analytics-performance-tracking') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-chart-pie" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Analytics & tracking</span>
                        <span class="beautage-admin-quicklink-desc">GA4, webmaster tools & Merchant feed</span>
                    </a>
                    @endif
                    @if($adminUser?->canManageAdminUsers())
                    <a href="{{ route('filament.admin.resources.users.index') }}" class="beautage-admin-quicklink">
                        <x-filament::icon icon="heroicon-o-user-circle" class="beautage-admin-quicklink-icon" />
                        <span class="beautage-admin-quicklink-title">Admin users</span>
                        <span class="beautage-admin-quicklink-desc">Roles & section permissions</span>
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
