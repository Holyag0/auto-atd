<?php

namespace App\Services;

use App\Models\Senha;
use App\Models\Setor;
use App\Models\ConfiguracaoSetor;
use Illuminate\Support\Facades\DB;

class SenhaService
{
    /**
     * Cria uma nova senha para o setor
     */
    public function criarSenha(int $setorId, string $nomeAssociado, ?string $codigoAcesso = null): Senha
    {
        return DB::transaction(function () use ($setorId, $nomeAssociado, $codigoAcesso) {
            $setor = Setor::findOrFail($setorId);
            
            // Busca ou cria configuração do setor
            $config = $setor->configuracao;
            
            if (!$config) {
                throw new \Exception('Setor não possui configuração de senhas.');
            }

            // Valida código de acesso se fornecido
            if ($codigoAcesso !== null && $config->codigo_acesso !== $codigoAcesso) {
                throw new \Exception('Código de acesso inválido.');
            }

            // Verifica se autoatendimento está ativo
            if ($codigoAcesso !== null && !$config->permite_autoatendimento) {
                throw new \Exception('Autoatendimento está desativado para este setor.');
            }

            // Gera próximo número
            $numero = $config->proximoNumero();
            $numeroCompleto = $config->formatarNumero($numero);

            // Cria a senha
            return Senha::create([
                'setor_id' => $setorId,
                'numero' => $numero,
                'numero_completo' => $numeroCompleto,
                'nome_associado' => $nomeAssociado,
                'status' => 'aguardando',
            ]);
        });
    }

    /**
     * Chama a próxima senha aguardando do setor
     */
    public function chamarProximaSenha(int $setorId, ?string $atendente = null): ?Senha
    {
        return DB::transaction(function () use ($setorId, $atendente) {
            // Marca senha atual como atendida se houver
            $senhaAtual = Senha::porSetor($setorId)
                ->chamando()
                ->first();
            
            if ($senhaAtual) {
                $senhaAtual->atender();
            }

            // Busca próxima senha aguardando
            $proximaSenha = Senha::porSetor($setorId)
                ->aguardando()
                ->oldest()
                ->first();

            if ($proximaSenha) {
                $proximaSenha->chamar($atendente);
            }

            return $proximaSenha;
        });
    }

    /**
     * Retorna a senha que está sendo chamada no momento
     */
    public function senhaAtualSetor(int $setorId): ?Senha
    {
        return Senha::porSetor($setorId)
            ->chamando()
            ->first();
    }

    /**
     * Retorna histórico das últimas senhas do setor
     */
    public function historicoSetor(int $setorId, int $limite = 5): array
    {
        return Senha::porSetor($setorId)
            ->whereIn('status', ['chamando', 'atendida'])
            ->hoje()
            ->latest('chamada_em')
            ->limit($limite)
            ->get()
            ->toArray();
    }

    /**
     * Retorna quantidade de senhas aguardando
     */
    public function quantidadeAguardando(int $setorId): int
    {
        return Senha::porSetor($setorId)
            ->aguardando()
            ->hoje()
            ->count();
    }

    /**
     * Reinicia o contador de senhas do setor
     */
    public function reiniciarContador(int $setorId): void
    {
        $setor = Setor::findOrFail($setorId);
        $config = $setor->configuracao;
        
        if ($config) {
            $config->resetarContador();
        }
    }

    /**
     * Cancela uma senha específica
     */
    public function cancelarSenha(int $senhaId): void
    {
        $senha = Senha::findOrFail($senhaId);
        $senha->cancelar();
    }

    /**
     * Atende uma senha específica
     */
    public function atenderSenha(int $senhaId): void
    {
        $senha = Senha::findOrFail($senhaId);
        $senha->atender();
    }

    /**
     * Retorna estatísticas do setor
     */
    public function estatisticasSetor(int $setorId): array
    {
        $hoje = today();

        return [
            'aguardando' => Senha::porSetor($setorId)->aguardando()->hoje()->count(),
            'atendidas_hoje' => Senha::porSetor($setorId)->atendidas()->hoje()->count(),
            'canceladas_hoje' => Senha::porSetor($setorId)->where('status', 'cancelada')->hoje()->count(),
            'total_hoje' => Senha::porSetor($setorId)->hoje()->count(),
        ];
    }

    /**
     * Cria ou atualiza configuração do setor
     */
    public function configurarSetor(int $setorId, array $dados): ConfiguracaoSetor
    {
        $setor = Setor::findOrFail($setorId);
        
        return $setor->configuracao()->updateOrCreate(
            ['setor_id' => $setorId],
            $dados
        );
    }

    /**
     * Gera novo código de acesso para o setor
     */
    public function gerarNovoCodigoAcesso(int $setorId): string
    {
        $setor = Setor::findOrFail($setorId);
        $config = $setor->configuracao;
        
        if (!$config) {
            throw new \Exception('Setor não possui configuração de senhas.');
        }

        $novoCodigo = ConfiguracaoSetor::gerarCodigoAcesso();
        $config->update(['codigo_acesso' => $novoCodigo]);
        
        return $novoCodigo;
    }
}
