<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Geral de Senhas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-8">Painéis de Atendimento</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($setores as $setor)
                @if($setor->configuracao && $setor->configuracao->ativo)
                <a href="{{ route('painel.setor', $setor->id) }}" 
                   class="block bg-white rounded-lg shadow-lg hover:shadow-xl transition-shadow p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-bold text-gray-800">{{ $setor->nome }}</h2>
                        @if($setor->sigla)
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $setor->sigla }}
                            </span>
                        @endif
                    </div>

                    @if($setor->senhas->isNotEmpty())
                        @php $senha = $setor->senhas->first(); @endphp
                        <div class="text-center py-6 bg-gray-50 rounded-lg">
                            <div class="text-5xl font-bold text-gray-900">{{ $senha->numero_completo }}</div>
                            <div class="text-sm text-gray-500 mt-2">Senha Atual</div>
                        </div>
                    @else
                        <div class="text-center py-6 bg-gray-50 rounded-lg">
                            <div class="text-3xl font-bold text-gray-400">---</div>
                            <div class="text-sm text-gray-500 mt-2">Aguardando...</div>
                        </div>
                    @endif

                    <div class="mt-4 flex items-center justify-center text-blue-600 hover:text-blue-700">
                        <span class="font-semibold">Ver Painel Completo →</span>
                    </div>
                </a>
                @endif
            @endforeach
        </div>

        @if($setores->isEmpty())
        <div class="text-center py-12">
            <p class="text-xl text-gray-500">Nenhum setor com sistema de senhas configurado</p>
        </div>
        @endif
    </div>
</body>
</html>
