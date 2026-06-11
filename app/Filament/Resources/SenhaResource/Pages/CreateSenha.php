<?php

namespace App\Filament\Resources\SenhaResource\Pages;

use App\Filament\Resources\SenhaResource;
use App\Services\SenhaService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateSenha extends CreateRecord
{
    protected static string $resource = SenhaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $senhaService = app(SenhaService::class);
        
        try {
            // Usa o serviço para criar a senha e pegar os números corretos
            $senha = $senhaService->criarSenha(
                $data['setor_id'],
                $data['nome_associado']
            );
            
            // Retorna os dados completos
            return array_merge($data, [
                'numero' => $senha->numero,
                'numero_completo' => $senha->numero_completo,
            ]);
            
        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro ao criar senha')
                ->body($e->getMessage())
                ->danger()
                ->send();
                
            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
