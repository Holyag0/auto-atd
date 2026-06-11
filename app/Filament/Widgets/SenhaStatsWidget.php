<?php

namespace App\Filament\Widgets;

use App\Models\Senha;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SenhaStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $hoje = today();

        return [
            Stat::make('Aguardando', Senha::aguardando()->hoje()->count())
                ->description('Senhas aguardando atendimento')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Chamando', Senha::chamando()->hoje()->count())
                ->description('Senhas sendo chamadas')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('info'),

            Stat::make('Atendidas Hoje', Senha::atendidas()->hoje()->count())
                ->description('Total de senhas atendidas hoje')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Hoje', Senha::hoje()->count())
                ->description('Total de senhas geradas hoje')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary'),
        ];
    }

    protected static ?int $sort = -1;
}
