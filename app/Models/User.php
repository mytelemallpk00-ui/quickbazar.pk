<?php

namespace App\Models;

use App\Support\AdminPermissions;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',
        'phone',
        'address',
        'cnic_no',
        'job_title',
        'is_admin',
        'is_super_admin',
        'admin_permissions',
    ];

    /** @var list<string> */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_super_admin' => 'boolean',
            'admin_permissions' => 'array',
        ];
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_admin) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->adminPermissions() !== [];
    }

    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    public static function superAdminExists(?int $exceptUserId = null): bool
    {
        $query = static::query()->where('is_super_admin', true);

        if ($exceptUserId) {
            $query->where('id', '!=', $exceptUserId);
        }

        return $query->exists();
    }

    public static function assertSingleSuperAdminSlot(?int $exceptUserId = null): void
    {
        if (static::superAdminExists($exceptUserId)) {
            throw new \RuntimeException('Only one super admin is allowed. Change the existing super admin to Admin first.');
        }
    }

    /** @return list<string> */
    public function adminPermissions(): array
    {
        if ($this->isSuperAdmin()) {
            return AdminPermissions::keys();
        }

        return AdminPermissions::normalize(is_array($this->admin_permissions) ? $this->admin_permissions : []);
    }

    public function canAccessAdminModule(string $permission): bool
    {
        if (! $this->is_admin) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return in_array($permission, $this->adminPermissions(), true);
    }

    public function canManageAdminUsers(): bool
    {
        return $this->is_admin && $this->isSuperAdmin();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->profilePhotoUrl();
    }

    public function profilePhotoUrl(): ?string
    {
        $path = trim((string) ($this->profile_photo ?? ''));
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return asset(ltrim($path, '/'));
        }

        if (str_starts_with($path, 'storage/')) {
            return asset($path);
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}
