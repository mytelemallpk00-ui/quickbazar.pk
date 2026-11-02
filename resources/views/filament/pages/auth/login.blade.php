<div class="beautage-login-shell">
    <div class="beautage-login-card">
        @include('filament.auth.partials.brand-panel')

        <div class="beautage-login-form-panel">
            <div class="beautage-login-form-inner">
                <div class="beautage-login-form-header">
                    <div class="beautage-login-lock">
                        <x-filament::icon icon="heroicon-o-lock-closed" class="beautage-login-lock-icon" />
                    </div>
                    <h2 class="beautage-login-form-title">Admin Sign In</h2>
                    <p class="beautage-login-form-subtitle">Enter your credentials to access the flagship dashboard.</p>
                </div>

                <div class="beautage-login-demo">
                    <div class="beautage-login-demo-copy">
                        <div class="beautage-login-demo-icon">
                            <x-filament::icon icon="heroicon-o-sparkles" class="beautage-login-demo-icon-svg" />
                        </div>
                        <div>
                            <span class="beautage-login-demo-label">Demo Assistant</span>
                            <span class="beautage-login-demo-hint">Click to auto-fill admin email</span>
                        </div>
                    </div>
                    <button type="button" wire:click="fillDemoCredentials" class="beautage-login-demo-btn">
                        Quick Fill
                    </button>
                </div>

                <x-filament-panels::form id="form" wire:submit="authenticate" class="beautage-login-filament-form">
                    {{ $this->form }}

                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()"
                    />
                </x-filament-panels::form>

                <p class="beautage-login-security-note">
                    Protected by Beautage Enterprise Security • Unauthorized access is logged and tracked.
                </p>
            </div>
        </div>
    </div>

    <x-filament-actions::modals />
</div>
