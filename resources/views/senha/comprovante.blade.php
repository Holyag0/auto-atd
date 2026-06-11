<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sua Senha - {{ $senha->numero_completo }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full" x-data="{ aguardando: {{ $naFrente }} }">
        <!-- Card Principal -->
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <!-- Header Success -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-8 text-center text-white">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold mb-2">Senha Gerada com Sucesso!</h1>
                <p class="text-green-100">Guarde este comprovante</p>
            </div>

            <!-- Senha -->
            <div class="p-8 text-center border-b-4 border-green-500">
                <p class="text-sm text-gray-600 mb-2 uppercase font-semibold">Sua Senha</p>
                <div class="text-8xl font-black text-gray-900 mb-4">{{ $senha->numero_completo }}</div>
                <p class="text-lg text-gray-600">{{ $senha->setor->nome }}</p>
            </div>

            <!-- Informações -->
            <div class="p-8 space-y-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Associado</p>
                    <p class="font-bold text-gray-900">{{ $senha->nome_associado }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-600 mb-1">Gerada em</p>
                    <p class="font-bold text-gray-900">{{ $senha->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div class="bg-yellow-50 rounded-lg p-4 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-yellow-800 mb-1">Senhas na sua frente</p>
                            <p class="text-3xl font-bold text-yellow-900" x-text="aguardando"></p>
                        </div>
                        <svg class="w-12 h-12 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>

                <!-- QR Code para Acompanhar -->
                <div class="bg-blue-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-blue-800 mb-3 font-semibold">Escaneie para acompanhar</p>
                    <div class="flex justify-center">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($urlPainel) }}" 
                             alt="QR Code Painel" 
                             class="rounded">
                    </div>
                </div>
            </div>

            <!-- Ações -->
            <div class="p-8 pt-0 space-y-3">
                <a href="{{ $urlPainel }}" 
                   class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-lg transition-colors text-center">
                    Acompanhar no Painel
                </a>

                <button onclick="window.print()" 
                        class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-6 rounded-lg transition-colors">
                    🖨️ Imprimir Comprovante
                </button>
            </div>
        </div>

        <!-- Informações Adicionais -->
        <div class="mt-6 bg-white rounded-lg shadow p-4">
            <h3 class="font-bold text-gray-800 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Instruções
            </h3>
            <ul class="text-sm text-gray-600 space-y-1">
                <li>• Fique atento ao painel de senhas</li>
                <li>• Aguarde sua senha ser chamada</li>
                <li>• Dirija-se ao guichê indicado</li>
            </ul>
        </div>

        <!-- Logo -->
        <div class="text-center mt-6">
            <p class="text-gray-600 font-semibold">CABEMCE</p>
        </div>
    </div>

    <!-- Atualizar aguardando a cada 5 segundos -->
    <script>
        setInterval(async () => {
            try {
                const response = await fetch('{{ route("painel.dados", $senha->setor_id) }}');
                const data = await response.json();
                
                // Atualiza contagem
                Alpine.store('aguardando', data.aguardando);
            } catch (error) {
                console.error('Erro ao atualizar:', error);
            }
        }, 5000);
    </script>

    <!-- Estilos para Impressão -->
    <style>
        @media print {
            body {
                background: white;
            }
            button {
                display: none;
            }
        }
    </style>
</body>
</html>
