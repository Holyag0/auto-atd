<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('configuracao_setors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setor_id')->constrained('setors')->onDelete('cascade');
            $table->integer('contador_atual')->default(0);
            $table->string('prefixo', 10)->nullable(); // Ex: "T" para Tesouraria
            $table->string('codigo_acesso', 20); // Código para associados gerarem senhas
            $table->boolean('permite_autoatendimento')->default(true);
            $table->text('mensagem_painel')->nullable(); // Mensagem customizada no painel
            $table->boolean('ativo')->default(true);
            $table->timestamps();
            
            $table->unique('setor_id');
            $table->index('ativo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('configuracao_setors');
    }
};
