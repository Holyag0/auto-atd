<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setor;
use App\Models\ConfiguracaoSetor;

class SenhaSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca setores existentes ou cria exemplos
        $setores = Setor::all();

        if ($setores->isEmpty()) {
            // Cria setores de exemplo se não existirem
            $setores = collect([
                Setor::create([
                    'nome' => 'Tesouraria',
                    'sigla' => 'TES',
                    'descricao' => 'Setor de Tesouraria e Pagamentos',
                    'ativo' => true,
                ]),
                Setor::create([
                    'nome' => 'Atendimento',
                    'sigla' => 'ATD',
                    'descricao' => 'Atendimento Geral',
                    'ativo' => true,
                ]),
                Setor::create([
                    'nome' => 'Documentação',
                    'sigla' => 'DOC',
                    'descricao' => 'Emissão de Documentos',
                    'ativo' => true,
                ]),
            ]);
        }

        // Cria configuração para cada setor
        foreach ($setores as $setor) {
            if (!$setor->configuracao) {
                ConfiguracaoSetor::create([
                    'setor_id' => $setor->id,
                    'contador_atual' => 0,
                    'prefixo' => substr($setor->sigla ?? 'S', 0, 1),
                    'codigo_acesso' => ConfiguracaoSetor::gerarCodigoAcesso(),
                    'permite_autoatendimento' => true,
                    'mensagem_painel' => 'Bem-vindo ao ' . $setor->nome,
                    'ativo' => true,
                ]);

                $this->command->info("✅ Configuração criada para: {$setor->nome}");
                $this->command->info("   Código de acesso: {$setor->fresh()->configuracao->codigo_acesso}");
            }
        }

        $this->command->info("\n🎉 Sistema de senhas configurado com sucesso!");
        $this->command->info("\nAcesse o painel admin em: /admin");
        $this->command->info("Gerencie as configurações em: Setores > Configuração de Senhas");
    }
}
