<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use App\Models\Senha;
use App\Services\SenhaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class PainelSenhaController extends Controller
{
    protected $senhaService;

    public function __construct(SenhaService $senhaService)
    {
        $this->senhaService = $senhaService;
    }

    /**
     * Exibe o painel geral com todos os setores
     */
    public function index()
    {
        $setores = Setor::ativos()
            ->with(['configuracao', 'senhas' => function ($query) {
                $query->chamando()->hoje();
            }])
            ->get();

        return view('painel.index', compact('setores'));
    }

    /**
     * Exibe o painel específico de um setor
     */
    public function setor($setorId)
    {
        $setor = Setor::with('configuracao')->findOrFail($setorId);
        
        if (!$setor->ativo) {
            abort(404, 'Setor não está ativo');
        }

        // Senha atual (chamando)
        $senhaAtual = $this->senhaService->senhaAtualSetor($setorId);
        
        // Histórico das últimas 5 senhas
        $historico = $this->senhaService->historicoSetor($setorId, 5);
        
        // Quantidade aguardando
        $aguardando = $this->senhaService->quantidadeAguardando($setorId);

        return view('painel.setor', compact('setor', 'senhaAtual', 'historico', 'aguardando'));
    }

    /**
     * API para atualização em tempo real (Server-Sent Events)
     */
    public function stream($setorId)
    {
        return Response::stream(function () use ($setorId) {
            while (true) {
                // Busca dados atuais
                $senhaAtual = $this->senhaService->senhaAtualSetor($setorId);
                $historico = $this->senhaService->historicoSetor($setorId, 5);
                $aguardando = $this->senhaService->quantidadeAguardando($setorId);

                $data = [
                    'senha_atual' => $senhaAtual,
                    'historico' => $historico,
                    'aguardando' => $aguardando,
                    'timestamp' => now()->toIso8601String(),
                ];

                echo "data: " . json_encode($data) . "\n\n";
                
                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

                // Aguarda 2 segundos antes da próxima atualização
                sleep(2);

                // Verifica se conexão ainda está ativa
                if (connection_aborted()) {
                    break;
                }
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * API JSON para obter dados do painel
     */
    public function dados($setorId)
    {
        $senhaAtual = $this->senhaService->senhaAtualSetor($setorId);
        $historico = $this->senhaService->historicoSetor($setorId, 5);
        $aguardando = $this->senhaService->quantidadeAguardando($setorId);

        return response()->json([
            'senha_atual' => $senhaAtual,
            'historico' => $historico,
            'aguardando' => $aguardando,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
