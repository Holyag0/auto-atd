<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Operador - {{ $setor->nome }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .senha-card {
            transition: all 0.3s ease;
        }
        .senha-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div x-data="operadorData()" x-init="init()">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg">
            <div class="container mx-auto px-4 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <a href="{{ route('operador.index') }}" class="text-blue-100 hover:text-white mb-2 inline-block">
                            ← Voltar aos setores
                        </a>
                        <h1 class="text-3xl font-bold">{{ $setor->nome }}</h1>
                        <p class="text-blue-100">Controle de Atendimento</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-blue-100">Atualiza a cada 3 segundos</p>
                        <p class="text-2xl font-bold" x-text="horaAtual"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="container mx-auto px-4 py-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Aguardando</p>
                            <p class="text-3xl font-bold text-yellow-600" x-text="estatisticas.aguardando"></p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Chamando</p>
                            <p class="text-3xl font-bold text-blue-600" x-text="senhaAtual ? 1 : 0"></p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Atendidas Hoje</p>
                            <p class="text-3xl font-bold text-green-600" x-text="estatisticas.atendidas_hoje"></p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Hoje</p>
                            <p class="text-3xl font-bold text-gray-700" x-text="estatisticas.total_hoje"></p>
                        </div>
                        <div class="bg-gray-100 rounded-full p-3">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Coluna 1: Senha Atual -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Senha Atual</h2>
                        
                        <template x-if="senhaAtual">
                            <div>
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white text-center mb-4">
                                    <p class="text-sm mb-2">Chamando</p>
                                    <p class="text-6xl font-black" x-text="senhaAtual.numero_completo"></p>
                                    <p class="text-lg mt-2" x-text="senhaAtual.nome_associado"></p>
                                </div>

                                <!-- Botão Atender -->
                                <button @click="atenderAtual()" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-lg transition-colors flex items-center justify-center space-x-2">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <span>Marcar como Atendida</span>
                                </button>
                            </div>
                        </template>

                        <template x-if="!senhaAtual">
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="text-gray-400 text-lg">Nenhuma senha sendo chamada</p>
                            </div>
                        </template>
                    </div>

                    <!-- Campo Nome do Atendente -->
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nome do Guichê/Atendente
                        </label>
                        <input type="text" 
                               x-model="nomeAtendente"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Ex: Guichê 01">
                    </div>
                </div>

                <!-- Coluna 2: Fila de Espera -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-gray-800">Fila de Espera</h2>
                            <button @click="chamarProxima()" 
                                    :disabled="senhasAguardando.length === 0"
                                    :class="senhasAguardando.length === 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'"
                                    class="text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center space-x-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                </svg>
                                <span>Chamar Próxima Senha</span>
                            </button>
                        </div>

                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            <template x-for="(senha, index) in senhasAguardando" :key="senha.id">
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 senha-card border-l-4 border-yellow-400">
                                    <div class="flex items-center space-x-4">
                                        <div class="bg-yellow-100 rounded-full w-10 h-10 flex items-center justify-center">
                                            <span class="font-bold text-yellow-700" x-text="index + 1"></span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-lg text-gray-800" x-text="senha.numero_completo"></p>
                                            <p class="text-sm text-gray-600" x-text="senha.nome_associado"></p>
                                            <p class="text-xs text-gray-400" x-text="formatarHora(senha.created_at)"></p>
                                        </div>
                                    </div>
                                    <button @click="cancelarSenha(senha.id)" 
                                            class="text-red-600 hover:text-red-700 p-2 rounded-lg hover:bg-red-50"
                                            title="Cancelar senha">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            <template x-if="senhasAguardando.length === 0">
                                <div class="text-center py-12">
                                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <p class="text-gray-400 text-lg">Nenhuma senha aguardando</p>
                                    <p class="text-gray-400 text-sm">As novas senhas aparecerão aqui automaticamente</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Links Rápidos -->
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('painel.setor', $setor->id) }}" 
                           target="_blank"
                           class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow flex items-center space-x-3">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800">Painel Público</p>
                                <p class="text-xs text-gray-500">Ver em nova aba</p>
                            </div>
                        </a>

                        <a href="{{ route('senha.qrcode', $setor->id) }}" 
                           target="_blank"
                           class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition-shadow flex items-center space-x-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-800">QR Code</p>
                                <p class="text-xs text-gray-500">Autoatendimento</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast de Notificação -->
        <div x-show="showToast" 
             x-transition
             class="fixed bottom-4 right-4 bg-white rounded-lg shadow-xl p-4 max-w-sm"
             style="display: none;">
            <div class="flex items-center space-x-3">
                <div :class="toastType === 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'" 
                     class="rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <p class="font-semibold text-gray-800" x-text="toastMessage"></p>
            </div>
        </div>
    </div>

    <script>
        function operadorData() {
            return {
                setorId: {{ $setor->id }},
                senhaAtual: @json($senhaAtual),
                senhasAguardando: @json($senhasAguardando),
                estatisticas: @json($estatisticas),
                nomeAtendente: 'Guichê 01',
                horaAtual: '',
                showToast: false,
                toastMessage: '',
                toastType: 'success',

                init() {
                    this.atualizarHora();
                    setInterval(() => this.atualizarHora(), 1000);
                    setInterval(() => this.atualizarDados(), 3000);
                },

                atualizarHora() {
                    const now = new Date();
                    this.horaAtual = now.toLocaleTimeString('pt-BR');
                },

                async atualizarDados() {
                    try {
                        const response = await fetch(`/operador/{{ $setor->id }}/dados`);
                        const data = await response.json();
                        
                        this.senhaAtual = data.senha_atual;
                        this.senhasAguardando = data.senhas_aguardando;
                        this.estatisticas = data.estatisticas;
                    } catch (error) {
                        console.error('Erro ao atualizar dados:', error);
                    }
                },

                async chamarProxima() {
                    try {
                        const response = await fetch(`/operador/{{ $setor->id }}/chamar-proxima`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                atendente: this.nomeAtendente
                            })
                        });

                        const data = await response.json();
                        
                        if (data.success) {
                            this.mostrarToast('Senha chamada com sucesso!', 'success');
                            await this.atualizarDados();
                        } else {
                            this.mostrarToast(data.message, 'error');
                        }
                    } catch (error) {
                        this.mostrarToast('Erro ao chamar senha', 'error');
                        console.error(error);
                    }
                },

                async atenderAtual() {
                    if (!confirm('Marcar senha como atendida?')) return;

                    try {
                        const response = await fetch(`/operador/{{ $setor->id }}/atender-atual`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();
                        
                        if (data.success) {
                            this.mostrarToast('Senha atendida!', 'success');
                            await this.atualizarDados();
                        } else {
                            this.mostrarToast(data.message, 'error');
                        }
                    } catch (error) {
                        this.mostrarToast('Erro ao atender senha', 'error');
                        console.error(error);
                    }
                },

                async cancelarSenha(senhaId) {
                    if (!confirm('Cancelar esta senha?')) return;

                    try {
                        const response = await fetch(`/operador/senha/${senhaId}/cancelar`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const data = await response.json();
                        
                        if (data.success) {
                            this.mostrarToast('Senha cancelada!', 'success');
                            await this.atualizarDados();
                        } else {
                            this.mostrarToast(data.message, 'error');
                        }
                    } catch (error) {
                        this.mostrarToast('Erro ao cancelar senha', 'error');
                        console.error(error);
                    }
                },

                formatarHora(datetime) {
                    return new Date(datetime).toLocaleTimeString('pt-BR', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                },

                mostrarToast(message, type = 'success') {
                    this.toastMessage = message;
                    this.toastType = type;
                    this.showToast = true;
                    setTimeout(() => {
                        this.showToast = false;
                    }, 3000);
                }
            }
        }
    </script>
</body>
</html>
