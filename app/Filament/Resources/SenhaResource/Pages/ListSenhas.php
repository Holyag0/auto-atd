<?php

namespace App\Filament\Resources\SenhaResource\Pages;

use App\Filament\Resources\SenhaResource;
use App\Services\SenhaService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListSenhas extends ListRecords
{
    protected static string $resource = SenhaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('limpar_antigas')
                ->label('Limpar Senhas Antigas')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Limpar senhas antigas')
                ->modalDescription('Isso irá remover todas as senhas com mais de 7 dias. Esta ação não pode ser desfeita.')
                ->action(function () {
                    $count = \App\Models\Senha::where('created_at', '<', now()->subDays(7))->delete();
                    
                    Notification::make()
                        ->title("$count senhas foram removidas")
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make()
                ->label('Nova Senha'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\SenhaStatsWidget::class,
        ];
    }
}
