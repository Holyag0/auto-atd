<?php

namespace Database\Seeders;

use App\Models\Setor;
use Illuminate\Database\Seeder;

class SetorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setores = [
            [
                'nome' => 'DIAF',
                'sigla' => 'DIAF',
                'descricao' => 'Diretoria Administrativa e Financeira',
                'ativo' => true,
            ],
            [
                'nome' => 'TESOURARIA',
                'sigla' => 'TESOU',
                'descricao' => 'Setor de Tesouraria',
                'ativo' => true,
            ],
            [
                'nome' => 'JURÍDICO',
                'sigla' => 'JUR',
                'descricao' => 'Setor Jurídico',
                'ativo' => true,
            ],
            [
                'nome' => 'PECÚLIO',
                'sigla' => 'PEC',
                'descricao' => 'Setor de Pecúlio',
                'ativo' => true,
            ],
            [
                'nome' => 'TI',
                'sigla' => 'TI',
                'descricao' => 'Tecnologia da Informação',
                'ativo' => true,
            ],
            [
                'nome' => 'PLANO DE SAÚDE',
                'sigla' => 'PS',
                'descricao' => 'Setor de Planos de Saúde',
                'ativo' => true,
            ],
        ];

        foreach ($setores as $setor) {
            Setor::updateOrCreate(
                ['nome' => $setor['nome']],
                $setor
            );
        }

        $this->command->info('✅ Setores criados com sucesso!');
    }
}
