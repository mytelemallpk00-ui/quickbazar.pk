<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\User;
use App\Support\AdminPermissions;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn (): bool => $this->getRecord()->id !== auth()->id())
                ->before(function (User $record): void {
                    if ($record->isSuperAdmin() && User::query()->where('is_super_admin', true)->count() <= 1) {
                        throw new \RuntimeException('You cannot delete the only super admin.');
                    }
                }),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['admin_role'] = $this->getRecord()->isSuperAdmin() ? 'super' : 'admin';
        $data['admin_permissions'] = $this->getRecord()->isSuperAdmin()
            ? []
            : ($this->getRecord()->admin_permissions ?? []);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        if (! auth()->user()?->canManageAdminUsers()) {
            unset($data['admin_role'], $data['admin_permissions'], $data['is_super_admin']);

            return $data;
        }

        $record = $this->getRecord();
        $isSuper = ($data['admin_role'] ?? 'admin') === 'super';

        if ($record->isSuperAdmin() && ! $isSuper && ! User::superAdminExists($record->id)) {
            throw new \RuntimeException('At least one super admin is required.');
        }

        if ($isSuper && ! $record->isSuperAdmin()) {
            User::assertSingleSuperAdminSlot($record->id);
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
