<?php

namespace BezhanSalleh\FilamentShield\Actions;

use Filament\Actions\Action;
use Filament\Support\Colors\Color;
use BezhanSalleh\FilamentShield\Support\Utils;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->name('export')
            ->color(Color::Blue)
            ->icon('heroicon-m-arrow-down-tray')
            ->label(__('filament-shield::filament-shield.actions.export'))
            ->requiresConfirmation()
            ->action(function (): StreamedResponse {
                $exportData = $this->export();

                return response()
                    ->streamDownload(
                        callback: function () use ($exportData) {
                            echo json_encode($exportData, JSON_PRETTY_PRINT);
                        },
                        name: 'roles_permissions_export.json',
                        headers: [
                            'Content-Type' => 'application/json',
                        ]
                    );
            });
    }

    private function export()
    {
        $roleModel = Utils::getRoleModel();

        $export = $roleModel::all()->mapWithKeys(fn($role) => [
            $role->name => $role->permissions()->pluck('name')->toArray()
        ]);

        return $export;
    }
}