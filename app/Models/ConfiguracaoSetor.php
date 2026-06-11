<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ConfiguracaoSetor extends Model
{
    use HasFactory;

    protected $fillable = [
        'setor_id',
        'contador_atual',
        'prefixo',
        'codigo_acesso',
        'permite_autoatendimento',
        'mensagem_painel',
        'ativo',
    ];

    protected $casts = [
        'permite_autoatendimento' => 'boolean',
        'ativo' => 'boolean',
        'contador_atual' => 'integer',
    ];

    /**
     * Relacionamento com setor
     */
    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    /**
     * Gera um novo código de acesso aleatório
     */
    public static function gerarCodigoAcesso(): string
    {
        return strtoupper(Str::random(8));
    }

    /**
     * Incrementa o contador e retorna o próximo número
     */
    public function proximoNumero(): int
    {
        $this->increment('contador_atual');
        return $this->contador_atual;
    }

    /**
     * Reseta o contador para zero
     */
    public function resetarContador(): void
    {
        $this->update(['contador_atual' => 0]);
    }

    /**
     * Formata o número da senha com o prefixo
     */
    public function formatarNumero(int $numero): string
    {
        $prefixo = $this->prefixo ?? substr($this->setor->sigla ?? 'S', 0, 1);
        return $prefixo . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }
}
