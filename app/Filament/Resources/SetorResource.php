<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SetorResource\Pages;
use App\Filament\Resources\SetorResource\RelationManagers;
use App\Models\Setor;
use App\Models\ConfiguracaoSetor;
use App\Services\SenhaService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SetorResource extends Resource
{
    protected static ?string $model = Setor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Sistema de Demandas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Informações Básicas')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\TextInput::make('nome')
                                    ->label('Nome')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('sigla')
                                    ->label('Sigla')
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('descricao')
                                    ->label('Descrição')
                                    ->maxLength(255),
                                Forms\Components\Toggle::make('ativo')
                                    ->label('Ativo')
                                    ->required(),
                            ]),

                        Forms\Components\Tabs\Tab::make('Configuração de Senhas')
                            ->icon('heroicon-o-ticket')
                            ->schema([
                                Forms\Components\Section::make('Configurações Gerais')
                                    ->schema([
                                        Forms\Components\TextInput::make('configuracao.prefixo')
                                            ->label('Prefixo das Senhas')
                                            ->helperText('Ex: T para Tesouraria (T001, T002...)')
                                            ->maxLength(10)
                                            ->placeholder('T')
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->configuracao) {
                                                    $component->state($record->configuracao->prefixo);
                                                }
                                            }),

                                        Forms\Components\Toggle::make('configuracao.permite_autoatendimento')
                                            ->label('Permitir Autoatendimento')
                                            ->helperText('Permite que associados gerem senhas via QR Code')
                                            ->default(true)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->configuracao) {
                                                    $component->state($record->configuracao->permite_autoatendimento);
                                                }
                                            }),

                                        Forms\Components\Toggle::make('configuracao.ativo')
                                            ->label('Sistema de Senhas Ativo')
                                            ->helperText('Ativa ou desativa o sistema de senhas para este setor')
                                            ->default(true)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->configuracao) {
                                                    $component->state($record->configuracao->ativo);
                                                }
                                            }),

                                        Forms\Components\Textarea::make('configuracao.mensagem_painel')
                                            ->label('Mensagem do Painel')
                                            ->helperText('Mensagem customizada exibida no painel público')
                                            ->rows(3)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->configuracao) {
                                                    $component->state($record->configuracao->mensagem_painel);
                                                }
                                            }),
                                    ])->columns(2),

                                Forms\Components\Section::make('Código de Acesso')
                                    ->description('Código necessário para associados gerarem senhas')
                                    ->schema([
                                        Forms\Components\TextInput::make('configuracao.codigo_acesso')
                                            ->label('Código Atual')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                if ($record && $record->configuracao) {
                                                    $component->state($record->configuracao->codigo_acesso);
                                                } else {
                                                    $component->state('Será gerado automaticamente');
                                                }
                                            }),

                                        Forms\Components\Placeholder::make('contador_info')
                                            ->label('Contador Atual')
                                            ->content(function ($record) {
                                                if ($record && $record->configuracao) {
                                                    return $record->configuracao->contador_atual;
                                                }
                                                return '0';
                                            }),
                                    ])->columns(2),
                            ])
                            ->visible(fn ($livewire) => $livewire instanceof Pages\EditSetor),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome')
                    ->searchable(),
                Tables\Columns\TextColumn::make('sigla')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descricao')
                    ->searchable(),
                Tables\Columns\IconColumn::make('ativo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('gerar_codigo')
                    ->label('Gerar Novo Código')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Gerar Novo Código de Acesso')
                    ->modalDescription('Um novo código será gerado. O código anterior será invalidado.')
                    ->action(function (Setor $record) {
                        $senhaService = app(SenhaService::class);
                        
                        if (!$record->configuracao) {
                            // Cria configuração se não existir
                            $record->configuracao()->create([
                                'codigo_acesso' => ConfiguracaoSetor::gerarCodigoAcesso(),
                                'prefixo' => substr($record->sigla ?? 'S', 0, 1),
                                'contador_atual' => 0,
                            ]);
                        } else {
                            $novoCodigo = $senhaService->gerarNovoCodigoAcesso($record->id);
                        }
                        
                        $record->refresh();
                        
                        Notification::make()
                            ->title('Novo código gerado!')
                            ->body('Código: ' . $record->configuracao->codigo_acesso)
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reiniciar_contador')
                    ->label('Reiniciar Contador')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reiniciar Contador de Senhas')
                    ->modalDescription('O contador será resetado para zero. Esta ação não pode ser desfeita.')
                    ->action(function (Setor $record) {
                        if ($record->configuracao) {
                            $record->configuracao->resetarContador();
                            
                            Notification::make()
                                ->title('Contador reiniciado!')
                                ->success()
                                ->send();
                        }
                    }),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('ver_qrcode')
                        ->label('Ver QR Code')
                        ->icon('heroicon-o-qr-code')
                        ->color('info')
                        ->url(fn (Setor $record): string => route('senha.qrcode', $record->id))
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('baixar_qrcode')
                        ->label('Baixar QR Code')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('success')
                        ->url(fn (Setor $record): string => 
                            "https://api.qrserver.com/v1/create-qr-code/?size=1000x1000&data=" . 
                            urlencode(route('senha.create', $record->id)) . 
                            "&download=1&filename=qrcode-{$record->sigla}.png"
                        )
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('ver_painel')
                        ->label('Ver Painel Público')
                        ->icon('heroicon-o-tv')
                        ->color('primary')
                        ->url(fn (Setor $record): string => route('painel.setor', $record->id))
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('controle_operador')
                        ->label('Controle do Operador')
                        ->icon('heroicon-o-computer-desktop')
                        ->color('warning')
                        ->url(fn (Setor $record): string => route('operador.painel', $record->id))
                        ->openUrlInNewTab(),
                ])->label('Acessos Rápidos')
                    ->icon('heroicon-o-link')
                    ->color('info')
                    ->button(),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSetors::route('/'),
            'create' => Pages\CreateSetor::route('/create'),
            'edit' => Pages\EditSetor::route('/{record}/edit'),
        ];
    }
}
