<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Senhas - {{ $setor->nome }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }

        .senha-display {
            font-size: 15rem;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -0.02em;
        }

        .guiche-display {
            font-size: 4rem;
            font-weight: 600;
        }

        .historico-item {
            transition: all 0.3s ease;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body class="bg-gray-100 overflow-hidden">
    <div 
        x-data="painelData()" 
        x-init="init()"
        class="h-screen flex"
    >
        <!-- Área Principal - 70% -->
        <div class="w-[70%] bg-white flex flex-col items-center justify-center p-12 relative">
            <!-- Logo/Nome do Setor -->
            <div class="absolute top-8 left-8">
                <h1 class="text-3xl font-bold text-gray-800">{{ $setor->nome }}</h1>
            </div>

            <!-- Tipo de Atendimento -->
            <div class="mb-8">
                <h2 class="text-6xl font-semibold text-gray-600 text-center">Normal</h2>
            </div>

            <!-- Senha Atual -->
            <template x-if="senhaAtual">
                <div class="text-center fade-in" :key="senhaAtual.numero_completo">
                    <div class="senha-display text-gray-900" x-text="senhaAtual.numero_completo"></div>
                    <div class="guiche-display text-gray-600 mt-8">
                        <span x-text="senhaAtual.atendido_por ? senhaAtual.atendido_por : 'Guichê 01'"></span>
                    </div>
                </div>
            </template>

            <template x-if="!senhaAtual">
                <div class="text-center text-gray-400">
                    <div class="senha-display">---</div>
                    <div class="guiche-display mt-8">Aguardando...</div>
                </div>
            </template>

            <!-- Mensagem Customizada -->
            @if($setor->configuracao && $setor->configuracao->mensagem_painel)
            <div class="absolute bottom-8 left-8 right-8">
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <p class="text-blue-700 text-lg">{{ $setor->configuracao->mensagem_painel }}</p>
                </div>
            </div>
            @endif

            <!-- Senhas Aguardando -->
            <div class="absolute bottom-8 right-8">
                <div class="bg-yellow-100 px-6 py-3 rounded-lg shadow-lg">
                    <p class="text-sm text-yellow-800 font-semibold">Aguardando</p>
                    <p class="text-4xl font-bold text-yellow-900" x-text="aguardando"></p>
                </div>
            </div>
        </div>

        <!-- Área de Histórico - 30% -->
        <div class="w-[30%] bg-gradient-to-b from-green-500 to-green-600 text-white flex flex-col">
            <!-- Header do Histórico -->
            <div class="p-8 border-b border-green-400">
                <h3 class="text-4xl font-bold">Histórico</h3>
            </div>

            <!-- Lista de Histórico -->
            <div class="flex-1 overflow-hidden">
                <template x-for="(item, index) in historico" :key="item.id">
                    <div class="border-b border-green-400 p-6 historico-item">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-5xl font-bold" x-text="item.numero_completo"></div>
                                <div class="text-2xl mt-2 text-green-100" 
                                     x-text="item.atendido_por ? item.atendido_por : 'Guichê 01'">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="historico.length === 0">
                    <div class="p-8 text-center text-green-200">
                        <p class="text-2xl">Nenhuma senha chamada ainda</p>
                    </div>
                </template>
            </div>

            <!-- Data e Hora -->
            <div class="p-8 border-t border-green-400 text-center">
                <div class="text-sm text-green-200 mb-2">
                    <span x-text="dataAtual"></span>
                </div>
                <div class="text-6xl font-bold" x-text="horaAtual"></div>
            </div>
        </div>
    </div>

    <script>
        function painelData() {
            return {
                senhaAtual: @json($senhaAtual),
                historico: @json($historico),
                aguardando: {{ $aguardando }},
                dataAtual: '',
                horaAtual: '',
                eventSource: null,

                init() {
                    this.atualizarHora();
                    setInterval(() => this.atualizarHora(), 1000);
                    this.conectarSSE();
                },

                atualizarHora() {
                    const now = new Date();
                    this.horaAtual = now.toLocaleTimeString('pt-BR', { 
                        hour: '2-digit', 
                        minute: '2-digit',
                        second: '2-digit'
                    });
                    this.dataAtual = now.toLocaleDateString('pt-BR', { 
                        day: '2-digit',
                        month: 'long',
                        year: 'numeric'
                    });
                },

                conectarSSE() {
                    // Usa polling ao invés de SSE para maior compatibilidade
                    setInterval(() => {
                        this.atualizarDados();
                    }, 3000); // Atualiza a cada 3 segundos
                },

                async atualizarDados() {
                    try {
                        // IMPORTANTE: Esta rota retorna apenas senhas do setor específico ($setor->id)
                        // Cada painel monitora apenas seu próprio setor, garantindo isolamento
                        const response = await fetch('{{ route("painel.dados", $setor->id) }}');
                        const data = await response.json();
                        
                        // Verifica se uma nova senha foi chamada neste setor específico
                        // O som só será reproduzido se a senha for deste setor
                        const senhaAnteriorId = this.senhaAtual?.id;
                        const senhaNovaId = data.senha_atual?.id;
                        
                        if (senhaAnteriorId !== senhaNovaId && senhaNovaId) {
                            // Nova senha foi chamada neste setor - toca o alerta sonoro
                            // O som só toca no painel do setor correspondente
                            this.senhaAtual = data.senha_atual;
                            this.tocarSom();
                        } else if (senhaAnteriorId !== senhaNovaId) {
                            // Senha foi removida (não toca som)
                            this.senhaAtual = data.senha_atual;
                        }
                        
                        this.historico = data.historico;
                        this.aguardando = data.aguardando;
                    } catch (error) {
                        console.error('Erro ao atualizar dados:', error);
                    }
                },

                tocarSom() {
                    // Toca o alerta sonoro quando uma nova senha é chamada neste setor específico
                    // Este som só será reproduzido no painel do setor correspondente
                    const audio = new Audio('/sounds/alerta-oxigeno.mp3');
                    audio.volume = 0.8; // Volume em 80%
                    audio.play().catch(error => {
                        console.log('Erro ao reproduzir som:', error);
                    });
                }
            }
        }
    </script>
</body>
</html>
