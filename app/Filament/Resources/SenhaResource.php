<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SenhaResource\Pages;
use App\Models\Senha;
use App\Models\Setor;
use App\Services\SenhaService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;

class SenhaResource extends Resource
{
    protected static ?string $model = Senha::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Senhas';

    protected static ?string $modelLabel = 'Senha';

    protected static ?string $pluralModelLabel = 'Senhas';

    protected static ?string $navigationGroup = 'Atendimento';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Senha')
                    ->schema([
                        Forms\Components\Select::make('setor_id')
                            ->label('Setor')
                            ->relationship('setor', 'nome')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('nome_associado')
                            ->label('Nome do Associado')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'aguardando' => 'Aguardando',
                                'chamando' => 'Chamando',
                                'atendida' => 'Atendida',
                                'cancelada' => 'Cancelada',
                            ])
                            ->default('aguardando')
                            ->required(),

                        Forms\Components\TextInput::make('atendido_por')
                            ->label('Atendido por')
                            ->maxLength(255),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero_completo')
                    ->label('Senha')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('lg'),

                Tables\Columns\TextColumn::make('setor.nome')
                    ->label('Setor')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nome_associado')
                    ->label('Associado')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'aguardando' => 'Aguardando',
                        'chamando' => 'Chamando',
                        'atendida' => 'Atendida',
                        'cancelada' => 'Cancelada',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'aguardando',
                        'info' => 'chamando',
                        'success' => 'atendida',
                        'danger' => 'cancelada',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('chamada_em')
                    ->label('Chamada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('atendida_em')
                    ->label('Atendida em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('atendido_por')
                    ->label('Atendente')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('setor_id')
                    ->label('Setor')
                    ->relationship('setor', 'nome')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'aguardando' => 'Aguardando',
                        'chamando' => 'Chamando',
                        'atendida' => 'Atendida',
                        'cancelada' => 'Cancelada',
                    ]),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('Criada de'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('chamar')
                    ->label('Chamar')
                    ->icon('heroicon-o-megaphone')
                    ->color('info')
                    ->visible(fn (Senha $record): bool => $record->status === 'aguardando')
                    ->requiresConfirmation()
                    ->action(function (Senha $record) {
                        $record->chamar(auth()->user()->name);
                        
                        Notification::make()
                            ->title('Senha chamada com sucesso!')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('atender')
                    ->label('Atender')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Senha $record): bool => $record->status === 'chamando')
                    ->requiresConfirmation()
                    ->action(function (Senha $record) {
                        $record->atender();
                        
                        Notification::make()
                            ->title('Senha atendida com sucesso!')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('cancelar')
                    ->label('Cancelar')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Senha $record): bool => in_array($record->status, ['aguardando', 'chamando']))
                    ->requiresConfirmation()
                    ->action(function (Senha $record) {
                        $record->cancelar();
                        
                        Notification::make()
                            ->title('Senha cancelada com sucesso!')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListSenhas::route('/'),
            'create' => Pages\CreateSenha::route('/create'),
            'edit' => Pages\EditSenha::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::aguardando()->hoje()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
