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
        Schema::create('senhas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setor_id')->constrained('setors')->onDelete('cascade');
            $table->integer('numero'); // Número sequencial (1, 2, 3...)
            $table->string('numero_completo', 20); // Formato com prefixo (T001, T002...)
            $table->string('nome_associado', 255);
            $table->enum('status', ['aguardando', 'chamando', 'atendida', 'cancelada'])->default('aguardando');
            $table->timestamp('chamada_em')->nullable();
            $table->timestamp('atendida_em')->nullable();
            $table->string('atendido_por')->nullable(); // Nome do atendente
            $table->timestamps();
            
            $table->index(['setor_id', 'status']);
            $table->index('created_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senhas');
    }
};
