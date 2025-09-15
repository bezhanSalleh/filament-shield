<?php

namespace BezhanSalleh\FilamentShield\Actions;

use Exception;
use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\FileUpload;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Symfony\Component\HttpFoundation\Exception\JsonException;

class ImportAction extends Action
{
    protected string $disk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->disk = config('filament-shield.import_disk', 'local');

        $this->name('import')
            ->color(Color::Green)
            ->icon('heroicon-m-arrow-up-tray')
            ->label(__('filament-shield::filament-shield.actions.import.label'))
            ->schema([
                Select::make('import_type')
                    ->label(__('filament-shield::filament-shield.actions.import.select.import_type.label'))
                    ->options([
                        'permissions' => __('filament-shield::filament-shield.actions.import.select.import_type.option.permissions'),
                        'roles_and_permissions' => __('filament-shield::filament-shield.actions.import.select.import_type.option.roles_and_permissions'),
                    ])
                    ->default('permissions')
                    ->required(),
                FileUpload::make('import')
                    ->label(__('filament-shield::filament-shield.actions.import.file_upload.import.label'))
                    ->acceptedFileTypes(['application/json', 'text/json'])
                    ->disk($this->disk)
                    ->visibility('private')
                    ->directory('filament-shield/imports')
                    ->required()
            ])
            ->modalSubmitActionLabel(__('filament-shield::filament-shield.actions.import.label'))
            ->action(function (array $data): void {
                $file = Storage::disk($this->disk)->path($data['import']);
                $content = File::get($file);
                $checkRoleExistance = $data['import_type'] === 'permissions';

                try {
                    $this->validateJson($content, $checkRoleExistance);
                } catch (Exception $e) {
                    Notification::make()
                        ->title(__('filament-shield::filament-shield.actions.import.errors.notification'))
                        ->danger()
                        ->send();

                    return ;
                }

                $this->importRoles($content);
                $this->importPermissions($content);

                Notification::make()
                    ->title(__('filament-shield::filament-shield.actions.import.success.notification'))
                    ->success()
                    ->send();
            });
    }

    private function importRoles(string $import)
    {
        $roleModel = Utils::getRoleModel();

        $import = json_decode($import, true);
        foreach ($import as $roleName => $permissions) {
            $role = $roleModel::firstOrCreate(['name' => $roleName]);
        }
    }

    private function importPermissions(string $import)
    {
        $roleModel = Utils::getRoleModel();

        $import = json_decode($import, true);
        foreach ($import as $roleName => $permissions) {
            $role = $roleModel::firstOrCreate(['name' => $roleName]);
            $permissions = Utils::getPermissionModel()::whereIn('name', $permissions)->pluck('id')->toArray();
            $role->permissions()->sync($permissions);
        }
    }

    private function validateJsonStructure(string $import): object
    {
        // Check if the JSON is valid
        $decoded = json_decode($import, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JsonException('Invalid JSON structure: ' . json_last_error_msg());
        }

        // Check the structure of the JSON
        if (!is_array($decoded)) {
            throw new JsonException('Invalid structure: JSON must decode to an associative array.');
        }

        foreach ($decoded as $role => $permissions) {
            if (!is_string($role) || !is_array($permissions)) {
                throw new JsonException('Invalid structure: Each role must be a string and permissions must be an array.');
            }
        }

        return (object) $decoded;
    }

    private function validateJson(string $import, bool $checkRoleExistance): bool
    {
        $import = $this->validateJsonStructure($import);

        $permissionModel = Utils::getPermissionModel();
        $roleModel = Utils::getRoleModel();
        $allPermissions = $permissionModel::pluck('name')->toArray();

        foreach ($import as $role => $permissions) {
            if ($checkRoleExistance && !$roleModel::where('name', $role)->exists()) {
                throw new Exception("Role '{$role}' does not exist in the system.");
            }

            foreach ($permissions as $permission) {
                if (!in_array($permission, $allPermissions)) {
                    throw new Exception("Permission '{$permission}' for role '{$role}' does not exist in the system.");
                }
            }
        }

        return true;
    }
}