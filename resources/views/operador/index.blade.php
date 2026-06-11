<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Atendimento - CABEMCE</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">Controle de Atendimento</h1>
            <p class="text-gray-600">Selecione seu setor para gerenciar as senhas</p>
        </div>

        <!-- Grid de Setores -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($setores as $setor)
            <a href="{{ route('operador.painel', $setor->id) }}" 
               class="block bg-white rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-1 overflow-hidden">
                
                <!-- Header do Card -->
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-2xl font-bold">{{ $setor->nome }}</h2>
                        @if($setor->sigla)
                            <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $setor->sigla }}
                            </span>
                        @endif
                    </div>
                    @if($setor->descricao)
                        <p class="text-blue-100 text-sm">{{ $setor->descricao }}</p>
                    @endif
                </div>

                <!-- Body do Card -->
                <div class="p-6">
                    @php
                        $aguardando = App\Models\Senha::porSetor($setor->id)->aguardando()->hoje()->count();
                        $senhaAtual = App\Models\Senha::porSetor($setor->id)->chamando()->first();
                    @endphp

                    <!-- Senha Atual -->
                    <div class="mb-4">
                        <p class="text-sm text-gray-500 mb-1">Senha Atual</p>
                        @if($senhaAtual)
                            <div class="text-4xl font-bold text-blue-600">
                                {{ $senhaAtual->numero_completo }}
                            </div>
                        @else
                            <div class="text-2xl text-gray-400">---</div>
                        @endif
                    </div>

                    <!-- Estatísticas -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-yellow-50 rounded-lg p-3 text-center">
                            <p class="text-sm text-yellow-800 font-semibold">Aguardando</p>
                            <p class="text-2xl font-bold text-yellow-900">{{ $aguardando }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 text-center">
                            @php
                                $atendidas = App\Models\Senha::porSetor($setor->id)->atendidas()->hoje()->count();
                            @endphp
                            <p class="text-sm text-green-800 font-semibold">Atendidas</p>
                            <p class="text-2xl font-bold text-green-900">{{ $atendidas }}</p>
                        </div>
                    </div>

                    <!-- Código de Acesso -->
                    @if($setor->configuracao)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500">Código de Acesso:</p>
                        <p class="text-sm font-mono font-bold text-gray-700">{{ $setor->configuracao->codigo_acesso }}</p>
                    </div>
                    @endif
                </div>

                <!-- Footer do Card -->
                <div class="bg-gray-50 px-6 py-4 flex items-center justify-between">
                    <span class="text-blue-600 font-semibold">Acessar Controle →</span>
                    <div class="flex space-x-2">
                        <a href="{{ route('painel.setor', $setor->id) }}" 
                           target="_blank"
                           onclick="event.stopPropagation()"
                           class="text-gray-500 hover:text-gray-700"
                           title="Ver Painel Público">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('senha.qrcode', $setor->id) }}" 
                           target="_blank"
                           onclick="event.stopPropagation()"
                           class="text-gray-500 hover:text-gray-700"
                           title="Ver QR Code">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        @if($setores->isEmpty())
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <p class="mt-4 text-xl text-gray-500">Nenhum setor disponível</p>
            <p class="text-gray-400">Configure os setores no painel administrativo</p>
        </div>
        @endif

        <!-- Links Úteis -->
        <div class="mt-12 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Links Úteis</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('painel.index') }}" 
                   class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-800">Painéis Públicos</p>
                        <p class="text-sm text-gray-600">Ver todos os painéis</p>
                    </div>
                </a>

                <a href="/admin" 
                   class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <svg class="w-6 h-6 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-800">Admin</p>
                        <p class="text-sm text-gray-600">Painel administrativo</p>
                    </div>
                </a>

                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="font-semibold text-gray-800">CABEMCE</p>
                        <p class="text-sm text-gray-600">Sistema de Senhas v1.0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
