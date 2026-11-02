<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Support\AdminPermissions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['is_admin'] = true;

        return $this->applyRoleFields($data);
    }

    /** @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function applyRoleFields(array $data): array
    {
        if (! auth()->user()?->canManageAdminUsers()) {
            unset($data['admin_role'], $data['admin_permissions'], $data['is_super_admin']);

            return $data;
        }

        $isSuper = ($data['admin_role'] ?? 'admin') === 'super';

        if ($isSuper) {
            User::assertSingleSuperAdminSlot();
        }

        $data['is_super_admin'] = $isSuper;

        if ($isSuper) {
            $data['admin_permissions'] = null;
        } else {
            $data['admin_permissions'] = AdminPermissions::normalize($data['admin_permissions'] ?? []);
        }

        unset($data['admin_role']);

        return $data;
    }
}
