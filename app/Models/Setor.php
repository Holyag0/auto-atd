<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'sigla',
        'descricao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Relacionamento com usuários
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'setor_id');
    }

    /**
     * Relacionamento com demandas
     */
    public function demandas(): HasMany
    {
        return $this->hasMany(Demanda::class, 'setor_id');
    }

    /**
     * Relacionamento com senhas
     */
    public function senhas(): HasMany
    {
        return $this->hasMany(Senha::class, 'setor_id');
    }

    /**
     * Relacionamento com configuração de senhas
     */
    public function configuracao()
    {
        return $this->hasOne(ConfiguracaoSetor::class, 'setor_id');
    }

    /**
     * Scope para setores ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }
}
