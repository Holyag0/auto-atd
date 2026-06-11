<?php

namespace App\Filament\Resources\SetorResource\Pages;

use App\Filament\Resources\SetorResource;
use App\Models\ConfiguracaoSetor;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetor extends EditRecord
{
    protected static string $resource = SetorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Carrega dados da configuração se existir
        $configuracao = $this->record->configuracao;
        
        if ($configuracao) {
            $data['configuracao'] = [
                'prefixo' => $configuracao->prefixo,
                'codigo_acesso' => $configuracao->codigo_acesso,
                'permite_autoatendimento' => $configuracao->permite_autoatendimento,
                'mensagem_painel' => $configuracao->mensagem_painel,
                'ativo' => $configuracao->ativo,
            ];
        }
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Extrai dados de configuração
        $configData = $data['configuracao'] ?? [];
        unset($data['configuracao']);
        
        // Salva ou atualiza configuração
        if (!empty($configData)) {
            // Se não tem código de acesso, gera um
            if (empty($configData['codigo_acesso'])) {
                $configData['codigo_acesso'] = ConfiguracaoSetor::gerarCodigoAcesso();
            }
            
            // Se não tem prefixo, usa primeira letra da sigla
            if (empty($configData['prefixo'])) {
                $configData['prefixo'] = substr($this->record->sigla ?? 'S', 0, 1);
            }
            
            $this->record->configuracao()->updateOrCreate(
                ['setor_id' => $this->record->id],
                $configData
            );
        }
        
        return $data;
    }
}
