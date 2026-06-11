<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Senha - {{ $setor->nome }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gradient-to-br from-green-50 to-blue-50 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                        </path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Gerar Senha</h1>
                <p class="text-gray-600">{{ $setor->nome }}</p>
            </div>

            <!-- Info Aguardando -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6 flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-800 font-semibold">Pessoas aguardando</p>
                    <p class="text-3xl font-bold text-blue-900">{{ $aguardando }}</p>
                </div>
                <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>

            <!-- Formulário -->
            <form method="POST" action="{{ route('senha.store', $setor->id) }}" x-data="{ loading: false }" 
                  @submit="loading = true">
                @csrf

                <!-- Mensagens de Erro -->
                @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-800 font-semibold">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}
                                @endforeach
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Nome -->
                <div class="mb-6">
                    <label for="nome_associado" class="block text-sm font-semibold text-gray-700 mb-2">
                        Seu Nome Completo
                    </label>
                    <input type="text" 
                           id="nome_associado" 
                           name="nome_associado" 
                           value="{{ old('nome_associado') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="Digite seu nome completo">
                </div>

                <!-- Código de Acesso -->
                <div class="mb-6">
                    <label for="codigo_acesso" class="block text-sm font-semibold text-gray-700 mb-2">
                        Código de Acesso
                    </label>
                    <input type="text" 
                           id="codigo_acesso" 
                           name="codigo_acesso" 
                           value="{{ old('codigo_acesso') }}"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="Digite o código fornecido"
                           maxlength="20">
                    <p class="text-xs text-gray-500 mt-1">
                        Código disponível presencialmente no local
                    </p>
                </div>

                <!-- Botão -->
                <button type="submit" 
                        :disabled="loading"
                        :class="loading ? 'bg-gray-400' : 'bg-green-600 hover:bg-green-700'"
                        class="w-full text-white font-bold py-4 px-6 rounded-lg transition-colors flex items-center justify-center">
                    <template x-if="!loading">
                        <span>Gerar Minha Senha</span>
                    </template>
                    <template x-if="loading">
                        <span class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Gerando...
                        </span>
                    </template>
                </button>
            </form>

            <!-- Links -->
            <div class="mt-6 text-center space-y-2">
                <a href="{{ route('painel.setor', $setor->id) }}" 
                   class="block text-blue-600 hover:text-blue-700 font-semibold text-sm">
                    Ver Painel de Senhas
                </a>
                <a href="{{ route('senha.qrcode', $setor->id) }}" 
                   class="block text-gray-600 hover:text-gray-700 text-sm">
                    ← Voltar para QR Code
                </a>
            </div>
        </div>

        <!-- Logo -->
        <div class="text-center mt-6">
            <p class="text-gray-600 font-semibold">CABEMCE</p>
        </div>
    </div>
</body>
</html>
