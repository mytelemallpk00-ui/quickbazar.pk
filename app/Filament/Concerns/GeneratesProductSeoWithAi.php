<?php

namespace App\Filament\Concerns;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductAi\ProductAiException;
use App\Services\ProductAi\ProductAiService;
use Filament\Actions;
use Filament\Notifications\Actions\Action as NotifyAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

trait GeneratesProductSeoWithAi
{
    protected function getGenerateSeoAiHeaderActions(): array
    {
        return [
            $this->getGenerateSeoAiAction(ProductAiService::PROVIDER_SMART, 'Smart SEO', 'success'),
            $this->getGenerateSeoAiAction(ProductAiService::PROVIDER_GEMINI_FLASH, 'Gemini Flash', 'info'),
            $this->getGenerateSeoAiAction(ProductAiService::PROVIDER_GEMINI_PRO, 'Gemini Pro', 'warning'),
            $this->getGenerateSeoAiAction(ProductAiService::PROVIDER_OPENAI, 'OpenAI', 'gray'),
        ];
    }

    protected function getGenerateSeoAiAction(string $provider, string $label, string $color): Actions\Action
    {
        $action = Actions\Action::make('generateSeoAi_'.$provider)
            ->label($label)
            ->icon('heroicon-o-sparkles')
            ->color($color)
            ->action(fn (): mixed => $this->runProductAiGeneration($provider))
            ->extraAttributes(['type' => 'button']);

        if ($provider === ProductAiService::PROVIDER_GEMINI_PRO) {
            $action
                ->requiresConfirmation()
                ->modalHeading('Generate with Gemini Pro')
                ->modalDescription('Uses your Google AI Studio API key (Settings → AI integration). Signing into Gmail in the browser cannot connect API access — paste the free API key from Google AI Studio instead.');
        } elseif ($provider !== ProductAiService::PROVIDER_SMART) {
            $action->requiresConfirmation();
        }

        return $action;
    }

    public function runProductAiGeneration(string $provider = ProductAiService::PROVIDER_SMART): void
    {
        /** @var ProductAiService $ai */
        $ai = app(ProductAiService::class);

        $state = $this->getProductFormRawState();
        $name = $this->resolveProductNameForAi($state);
        if ($name === '') {
            Notification::make()
                ->title('Product name required')
                ->body('Type a product name in the Name field (or add a target keyword / specs in the AI box), then try again.')
                ->warning()
                ->send();

            return;
        }

        if ($provider !== ProductAiService::PROVIDER_SMART && ! $ai->isConfigured($provider)) {
            Notification::make()
                ->title('API key needed for this option')
                ->body('Use Smart SEO (no key) or add keys under Settings → AI integration.')
                ->warning()
                ->actions([
                    NotifyAction::make('openAiSettings')
                        ->label('Open AI integration')
                        ->url(route('filament.admin.pages.ai-integration')),
                    NotifyAction::make('googleAiStudio')
                        ->label('Get free Gemini key')
                        ->url('https://aistudio.google.com/app/apikey')
                        ->openUrlInNewTab(),
                ])
                ->send();

            return;
        }

        $keyword = trim((string) ($state['ai_target_keyword'] ?? $state['seo_focus_keyword'] ?? ''));
        $specs = trim((string) ($state['ai_specs_context'] ?? ''));
        if ($specs === '' && is_array($state['specs_rows'] ?? null)) {
            $specs = collect($state['specs_rows'])
                ->map(fn ($row): string => trim(($row['label'] ?? '').': '.($row['value'] ?? '')))
                ->filter()
                ->implode("\n");
        }

        $categoryName = null;
        if (! empty($state['category_id'])) {
            $categoryName = Category::query()->whereKey($state['category_id'])->value('name');
        }

        try {
            $content = $ai->generate(
                $name,
                $keyword !== '' ? $keyword : null,
                $specs !== '' ? $specs : null,
                $categoryName,
                $provider,
            );
            $merged = array_merge($state, $content->toFormState());

            if ($keyword !== '') {
                $merged['seo_focus_keyword'] = $keyword;
            } elseif ($content->focusKeyword !== '') {
                $merged['seo_focus_keyword'] = $content->focusKeyword;
                $merged['ai_target_keyword'] = $content->focusKeyword;
            }

            $recordId = method_exists($this, 'getRecord') ? $this->getRecord()?->getKey() : null;
            $merged['slug'] = $this->resolveUniqueProductSlug($content->slug, $recordId);

            $merged['name'] = $merged['name'] ?? $name;

            $this->form->fill($merged);

            if (property_exists($this, 'data') && is_array($this->data)) {
                $this->data = array_merge($this->data, $merged);
            }

            $this->resetErrorBag();
            $this->resetValidation();

            $label = match ($provider) {
                ProductAiService::PROVIDER_SMART => 'Smart SEO (no API key)',
                ProductAiService::PROVIDER_GEMINI_FLASH => 'Gemini Flash',
                ProductAiService::PROVIDER_GEMINI_PRO => 'Gemini Pro',
                ProductAiService::PROVIDER_OPENAI => 'OpenAI',
                default => 'AI',
            };

            Notification::make()
                ->title('Content generated · '.$label)
                ->body('Review SEO, slug, and detail tabs, then save the product.')
                ->success()
                ->send();
        } catch (ProductAiException $e) {
            Notification::make()
                ->title('Generation failed')
                ->body($e->getMessage())
                ->danger()
                ->actions([
                    NotifyAction::make('settings')
                        ->label('AI integration settings')
                        ->url(route('filament.admin.pages.ai-integration')),
                ])
                ->send();
        } catch (\Throwable $e) {
            report($e);
            Notification::make()
                ->title('Generation failed')
                ->body('Something went wrong. Try Smart SEO or check AI keys.')
                ->danger()
                ->send();
        }
    }

    /** @return array<string, mixed> */
    protected function getProductFormRawState(): array
    {
        try {
            return $this->form->getRawState();
        } catch (\Throwable) {
            return is_array($this->data ?? null) ? $this->data : [];
        }
    }

    /** @param  array<string, mixed>  $state */
    protected function resolveProductNameForAi(array $state): string
    {
        $name = trim((string) ($state['name'] ?? ''));
        if ($name !== '') {
            return $name;
        }

        $keyword = trim((string) ($state['ai_target_keyword'] ?? $state['seo_focus_keyword'] ?? ''));
        if ($keyword !== '') {
            return $keyword;
        }

        $specs = trim((string) ($state['ai_specs_context'] ?? ''));
        if ($specs !== '') {
            $firstLine = trim(strtok($specs, "\r\n") ?: '');

            return $firstLine !== '' ? Str::limit($firstLine, 200) : '';
        }

        return '';
    }

    protected function resolveUniqueProductSlug(string $baseSlug, mixed $ignoreId = null): string
    {
        $slug = Str::slug($baseSlug);
        if ($slug === '') {
            $slug = 'product';
        }
        $slug = Str::limit($slug, 80, '');

        $query = Product::query()->where('slug', $slug);
        if ($ignoreId) {
            $query->whereKeyNot($ignoreId);
        }

        if (! $query->exists()) {
            return $slug;
        }

        $i = 2;
        while (Product::query()->where('slug', $slug.'-'.$i)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $i++;
        }

        return Str::limit($slug.'-'.$i, 80, '');
    }
}
