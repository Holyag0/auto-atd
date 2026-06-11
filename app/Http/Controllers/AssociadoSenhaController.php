<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use App\Models\Senha;
use App\Services\SenhaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AssociadoSenhaController extends Controller
{
    protected $senhaService;

    public function __construct(SenhaService $senhaService)
    {
        $this->senhaService = $senhaService;
    }

    /**
     * Exibe a página com QR Code do setor
     */
    public function qrcode($setorId)
    {
        $setor = Setor::with('configuracao')->findOrFail($setorId);
        
        if (!$setor->ativo || !$setor->configuracao) {
            abort(404, 'Setor não disponível');
        }

        if (!$setor->configuracao->permite_autoatendimento) {
            abort(403, 'Autoatendimento não disponível para este setor');
        }

        // URL para o formulário de senha
        $url = route('senha.formulario', $setorId);

        return view('senha.qrcode', compact('setor', 'url'));
    }

    /**
     * Exibe o formulário para tirar senha
     */
    public function formulario($setorId)
    {
        $setor = Setor::with('configuracao')->findOrFail($setorId);
        
        if (!$setor->ativo || !$setor->configuracao) {
            abort(404, 'Setor não disponível');
        }

        if (!$setor->configuracao->permite_autoatendimento) {
            abort(403, 'Autoatendimento não disponível para este setor');
        }

        // Estatísticas
        $aguardando = $this->senhaService->quantidadeAguardando($setorId);

        return view('senha.formulario', compact('setor', 'aguardando'));
    }

    /**
     * Cria uma nova senha
     */
    public function store(Request $request, $setorId)
    {
        // Rate limiting: máximo 3 senhas por IP a cada 10 minutos
        $key = 'criar-senha:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            
            throw ValidationException::withMessages([
                'rate_limit' => "Você tentou criar muitas senhas. Aguarde {$seconds} segundos.",
            ]);
        }

        $request->validate([
            'nome_associado' => 'required|string|max:255',
            'codigo_acesso' => 'required|string',
        ], [
            'nome_associado.required' => 'O nome é obrigatório',
            'codigo_acesso.required' => 'O código de acesso é obrigatório',
        ]);

        try {
            $senha = $this->senhaService->criarSenha(
                $setorId,
                $request->nome_associado,
                $request->codigo_acesso
            );

            // Incrementa o rate limiter
            RateLimiter::hit($key, 600); // 10 minutos

            return redirect()->route('senha.comprovante', $senha->id)
                ->with('success', 'Senha gerada com sucesso!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Exibe o comprovante da senha gerada
     */
    public function comprovante($senhaId)
    {
        $senha = Senha::with(['setor', 'setor.configuracao'])->findOrFail($senhaId);
        
        // Quantidade de senhas na frente
        $naFrente = Senha::where('setor_id', $senha->setor_id)
            ->where('status', 'aguardando')
            ->where('numero', '<', $senha->numero)
            ->hoje()
            ->count();

        // URL do painel para acompanhar
        $urlPainel = route('painel.setor', $senha->setor_id);

        return view('senha.comprovante', compact('senha', 'naFrente', 'urlPainel'));
    }
}
