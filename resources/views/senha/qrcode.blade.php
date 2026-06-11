<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code - {{ $setor->nome }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-2xl shadow-2xl p-8 md:p-12 text-center">
            <!-- Header -->
            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z">
                        </path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">{{ $setor->nome }}</h1>
                <p class="text-xl text-gray-600">Sistema de Senhas</p>
            </div>

            <!-- QR Code -->
            <div class="bg-gray-50 rounded-xl p-8 mb-8">
                <div class="flex justify-center mb-4">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={{ urlencode($url) }}" 
                         alt="QR Code" 
                         class="rounded-lg shadow-md">
                </div>
                <p class="text-sm text-gray-500">Escaneie o QR Code para gerar sua senha</p>
            </div>

            <!-- Instruções -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-8 text-left">
                <h3 class="font-bold text-blue-900 mb-3 text-lg">📱 Como usar:</h3>
                <ol class="space-y-2 text-blue-800">
                    <li class="flex items-start">
                        <span class="font-bold mr-2">1.</span>
                        <span>Escaneie o QR Code com seu celular</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">2.</span>
                        <span>Preencha seu nome e o código de acesso</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-bold mr-2">3.</span>
                        <span>Receba sua senha e acompanhe no painel</span>
                    </li>
                </ol>
            </div>

            <!-- Código de Acesso -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 mb-8">
                <p class="text-sm text-yellow-800 mb-2">
                    <strong>⚠️ Atenção:</strong> Você precisará de um código de acesso fornecido presencialmente.
                </p>
                @if($setor->configuracao && $setor->configuracao->mensagem_painel)
                <p class="text-sm text-yellow-700 mt-2">{{ $setor->configuracao->mensagem_painel }}</p>
                @endif
            </div>

            <!-- Link Direto -->
            <div class="pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500 mb-4">Ou acesse diretamente:</p>
                <a href="{{ $url }}" 
                   class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-8 rounded-lg transition-colors">
                    Gerar Senha
                </a>
            </div>

            <!-- Link Painel -->
            <div class="mt-6">
                <a href="{{ route('painel.setor', $setor->id) }}" 
                   class="text-blue-600 hover:text-blue-700 font-semibold">
                    Ver Painel de Senhas →
                </a>
            </div>
        </div>

        <!-- Logo CABEMCE -->
        <div class="text-center mt-8">
            <p class="text-gray-600 font-semibold">CABEMCE</p>
            <p class="text-sm text-gray-500">Caixa de Assistência dos Empregados da MCE</p>
        </div>
    </div>
</body>
</html>
