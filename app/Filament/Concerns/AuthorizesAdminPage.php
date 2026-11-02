<?php

namespace App\Filament\Concerns;

use App\Models\User;
use App\Support\AdminPermissions;

trait AuthorizesAdminPage
{
    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::OVERVIEW;
    }

    protected static function resolveAdminPermissionKey(): string
    {
        $reflection = new \ReflectionClass(static::class);

        if ($reflection->hasProperty('adminPermission')) {
            $property = $reflection->getProperty('adminPermission');

            if ($property->isStatic()) {
                if ($property->hasDefaultValue()) {
                    return (string) $property->getDefaultValue();
                }

                if ($property->isInitialized()) {
                    return (string) $property->getValue();
                }
            }
        }

        $moduleMethod = $reflection->getMethod('adminPermissionModule');
        if ($moduleMethod->getDeclaringClass()->getName() !== self::class) {
            return static::adminPermissionModule();
        }

        return AdminPermissions::OVERVIEW;
    }

    protected static function adminAuthUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    public static function canAccess(): bool
    {
        $user = static::adminAuthUser();

        if (! $user?->is_admin) {
            return false;
        }

        return $user->canAccessAdminModule(static::resolveAdminPermissionKey());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
