<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use App\Models\Senha;
use App\Services\SenhaService;
use Illuminate\Http\Request;

class OperadorSenhaController extends Controller
{
    protected $senhaService;

    public function __construct(SenhaService $senhaService)
    {
        $this->senhaService = $senhaService;
    }

    /**
     * Index - Lista todos os setores para escolher
     */
    public function index()
    {
        $setores = Setor::ativos()
            ->with('configuracao')
            ->get()
            ->filter(function ($setor) {
                return $setor->configuracao && $setor->configuracao->ativo;
            });

        return view('operador.index', compact('setores'));
    }

    /**
     * Painel de controle do operador para um setor específico
     */
    public function painel($setorId)
    {
        $setor = Setor::with('configuracao')->findOrFail($setorId);
        
        if (!$setor->ativo || !$setor->configuracao || !$setor->configuracao->ativo) {
            abort(404, 'Setor não disponível');
        }

        // Senha atual (chamando)
        $senhaAtual = $this->senhaService->senhaAtualSetor($setorId);
        
        // Próximas senhas aguardando
        $senhasAguardando = Senha::porSetor($setorId)
            ->aguardando()
            ->hoje()
            ->orderBy('created_at')
            ->limit(10)
            ->get();
        
        // Histórico de hoje
        $historicoHoje = Senha::porSetor($setorId)
            ->atendidas()
            ->hoje()
            ->latest('atendida_em')
            ->limit(10)
            ->get();
        
        // Estatísticas
        $estatisticas = $this->senhaService->estatisticasSetor($setorId);

        return view('operador.painel', compact(
            'setor',
            'senhaAtual',
            'senhasAguardando',
            'historicoHoje',
            'estatisticas'
        ));
    }

    /**
     * Chamar próxima senha
     */
    public function chamarProxima(Request $request, $setorId)
    {
        try {
            $atendente = $request->input('atendente', 'Guichê 01');
            
            $senha = $this->senhaService->chamarProximaSenha($setorId, $atendente);
            
            if ($senha) {
                return response()->json([
                    'success' => true,
                    'message' => 'Senha chamada com sucesso!',
                    'senha' => $senha
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma senha aguardando'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao chamar senha: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atender senha atual
     */
    public function atenderAtual(Request $request, $setorId)
    {
        try {
            $senhaAtual = $this->senhaService->senhaAtualSetor($setorId);
            
            if ($senhaAtual) {
                $this->senhaService->atenderSenha($senhaAtual->id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Senha atendida com sucesso!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma senha sendo chamada'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atender senha: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar uma senha
     */
    public function cancelar($senhaId)
    {
        try {
            $this->senhaService->cancelarSenha($senhaId);
            
            return response()->json([
                'success' => true,
                'message' => 'Senha cancelada com sucesso!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao cancelar senha: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API para atualizar dados do painel
     */
    public function dados($setorId)
    {
        $senhaAtual = $this->senhaService->senhaAtualSetor($setorId);
        
        $senhasAguardando = Senha::porSetor($setorId)
            ->aguardando()
            ->hoje()
            ->orderBy('created_at')
            ->limit(10)
            ->get();
        
        $estatisticas = $this->senhaService->estatisticasSetor($setorId);

        return response()->json([
            'senha_atual' => $senhaAtual,
            'senhas_aguardando' => $senhasAguardando,
            'estatisticas' => $estatisticas,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
