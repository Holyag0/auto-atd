<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Senha extends Model
{
    use HasFactory;

    protected $fillable = [
        'setor_id',
        'numero',
        'numero_completo',
        'nome_associado',
        'status',
        'chamada_em',
        'atendida_em',
        'atendido_por',
    ];

    protected $casts = [
        'chamada_em' => 'datetime',
        'atendida_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relacionamento com setor
     */
    public function setor(): BelongsTo
    {
        return $this->belongsTo(Setor::class);
    }

    /**
     * Scope para senhas aguardando
     */
    public function scopeAguardando($query)
    {
        return $query->where('status', 'aguardando');
    }

    /**
     * Scope para senhas chamando
     */
    public function scopeChamando($query)
    {
        return $query->where('status', 'chamando');
    }

    /**
     * Scope para senhas atendidas
     */
    public function scopeAtendidas($query)
    {
        return $query->where('status', 'atendida');
    }

    /**
     * Scope para senhas de hoje
     */
    public function scopeHoje($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope por setor
     */
    public function scopePorSetor($query, $setorId)
    {
        return $query->where('setor_id', $setorId);
    }

    /**
     * Chama a senha
     */
    public function chamar(?string $atendente = null): void
    {
        $this->update([
            'status' => 'chamando',
            'chamada_em' => now(),
            'atendido_por' => $atendente,
        ]);
    }

    /**
     * Marca senha como atendida
     */
    public function atender(): void
    {
        $this->update([
            'status' => 'atendida',
            'atendida_em' => now(),
        ]);
    }

    /**
     * Cancela a senha
     */
    public function cancelar(): void
    {
        $this->update([
            'status' => 'cancelada',
        ]);
    }

    /**
     * Retorna badge de cor por status
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'aguardando' => 'warning',
            'chamando' => 'info',
            'atendida' => 'success',
            'cancelada' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Retorna label traduzido do status
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'aguardando' => 'Aguardando',
            'chamando' => 'Chamando',
            'atendida' => 'Atendida',
            'cancelada' => 'Cancelada',
            default => 'Desconhecido',
        };
    }
}
