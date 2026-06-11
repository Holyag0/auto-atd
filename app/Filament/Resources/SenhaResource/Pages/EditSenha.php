<?php

namespace App\Filament\Resources\SenhaResource\Pages;

use App\Filament\Resources\SenhaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSenha extends EditRecord
{
    protected static string $resource = SenhaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
