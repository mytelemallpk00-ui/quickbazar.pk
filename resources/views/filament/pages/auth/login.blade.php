<div class="sp-login">
    @include('filament.auth.partials.brand-panel')

    <section class="sp-login-panel">
        <div class="sp-login-card">
            <p class="sp-login-kicker">Staff sign in</p>
            <h2 class="sp-login-title">Welcome back</h2>
            <p class="sp-login-lead">Use your Shop Pakistan admin account to open the dashboard.</p>

            <x-filament-panels::form id="form" wire:submit="authenticate" class="sp-login-form">
                {{ $this->form }}

                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>

            <p class="sp-login-note">This area is for store staff. Sign-in attempts are recorded.</p>
        </div>
    </section>

    <x-filament-actions::modals />
</div>
