# Roadmap: Migração do Sistema de Senhas para Projeto Dedicado

## Data: 2026-06-11

## 1. Visão Geral do Sistema Atual

O sistema de senhas atual está integrado ao projeto `faq-cabemce` e gerencia filas de atendimento para múltiplos setores. O sistema permite:

- **Autoatendimento**: Associados geram senhas via formulário web (com código de acesso)
- **Painel Público**: Exibe senhas sendo chamadas em tempo real
- **Controle do Operador**: Interface para chamar/atender senhas
- **Administração via Filament**: Gerenciamento completo via painel administrativo

### Fluxo Principal

```
Associado → QR Code → Formulário → Código Acesso → Senha Gerada
                                                   ↓
Operador → Painel → Chamar Próxima → Senha Chamando → Atender → Senha Atendida
                                         ↓
Painel Público → Server-Sent Events → Exibe Senha Atual em Tempo Real
```

---

## 2. Análise dos Componentes Atuais

### 2.1 Models

| Model | Arquivo | Responsabilidade |
|-------|---------|-------------------|
| `Senha` | `app/Models/Senha.php` | Entidade principal da senha |
| `Setor` | `app/Models/Setor.php` | Setores de atendimento |
| `ConfiguracaoSetor` | `app/Models/ConfiguracaoSetor.php` | Configurações por setor |
| `User` | `app/Models/User.php` | Usuários/operadores (relacionamento) |

### 2.2 Migrations

| Migration | Descrição |
|-----------|-----------|
| `2026_01_14_172811_create_setors_table.php` | Tabela de setores |
| `2026_01_16_000001_create_configuracao_setors_table.php` | Configurações de senhas por setor |
| `2026_01_16_000002_create_senhas_table.php` | Tabela principal de senhas |
| `2026_01_14_173126_add_setor_and_cargo_to_users_table.php` | Relacionamento usuário-setor |

### 2.3 Services

| Service | Arquivo | Responsabilidade |
|---------|---------|-------------------|
| `SenhaService` | `app/Services/SenhaService.php` | Regras de negócio das senhas |

**Principais Métodos:**
- `criarSenha()` - Cria nova senha com validação
- `chamarProximaSenha()` - Chama próxima da fila
- `senhaAtualSetor()` - Retorna senha sendo chamada
- `quantidadeAguardando()` - Contador de espera
- `estatisticasSetor()` - Métricas do setor
- `gerarNovoCodigoAcesso()` - Regenera código de acesso

### 2.4 Controllers

| Controller | Rotas | Responsabilidade |
|------------|-------|-------------------|
| `AssociadoSenhaController` | `/senha/{setor}` | Interface do associado (QR, formulário, comprovante) |
| `OperadorSenhaController` | `/operador` | Painel de controle do operador |
| `PainelSenhaController` | `/painel` | Painel público com SSE |

### 2.5 Filament Resources

| Resource | Arquivo | Responsabilidade |
|----------|---------|-------------------|
| `SenhaResource` | `app/Filament/Resources/SenhaResource.php` | CRUD de senhas |
| `SetorResource` | `app/Filament/Resources/SetorResource.php` | CRUD de setores + configuração |

### 2.6 Views (Blade)

| View | Caminho | Responsabilidade |
|------|---------|-------------------|
| `formulario.blade.php` | `resources/views/senha/` | Formulário para tirar senha |
| `comprovante.blade.php` | `resources/views/senha/` | Comprovante da senha gerada |
| `qrcode.blade.php` | `resources/views/senha/` | Display do QR Code |
| `setor.blade.php` | `resources/views/painel/` | Painel público do setor |
| `index.blade.php` | `resources/views/painel/` | Painel geral (todos setores) |
| `painel.blade.php` | `resources/views/operador/` | Painel do operador |

### 2.7 Dependências

```json
{
  "filament/filament": "^3.0",
  "simplesoftwareio/simple-qrcode": "^4.2",
  "laravel/framework": "^12.0",
  "laravel/sail": "^1.51"
}
```

---

## 3. Estrutura do Novo Projeto

### 3.1 Nome e Localização

- **Nome**: `auto-atd`
- **Localização**: `/Users/holy/projetos/auto-zap/auto-atd`
- **Stack**: Laravel + Filament + Sail (Docker)

### 3.2 Estrutura de Diretórios Proposta

```
auto-atd/
├── app/
│   ├── Models/
│   │   ├── Setor.php
│   │   ├── ConfiguracaoSetor.php
│   │   ├── Senha.php
│   │   └── Usuario.php (renomeado de User)
│   ├── Services/
│   │   └── SenhaService.php
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AssociadoSenhaController.php
│   │       ├── OperadorSenhaController.php
│   │       └── PainelSenhaController.php
│   └── Filament/
│       └── Resources/
│           ├── SenhaResource.php
│           └── SetorResource.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_setors_table.php
│   │   ├── 2024_01_01_000002_create_configuracao_setors_table.php
│   │   ├── 2024_01_01_000003_create_senhas_table.php
│   │   └── 2024_01_01_000004_create_users_table.php
│   └── seeders/
│       ├── SetorSeeder.php
│       ├── SenhaSystemSeeder.php
│       └── UserSeeder.php
├── resources/
│   └── views/
│       ├── senha/
│       │   ├── formulario.blade.php
│       │   ├── comprovante.blade.php
│       │   └── qrcode.blade.php
│       ├── painel/
│       │   ├── index.blade.php
│       │   └── setor.blade.php
│       └── operador/
│           └── painel.blade.php
├── docker/
│   ├── 8.5/
│   │   └── Dockerfile
│   ├── php/
│   │   └── php.ini
│   └── pgsql/
│       └── create-testing-database.sql
├── routes/
│   └── web.php
├── compose.yaml
├── Dockerfile
└── README.md
```

---

## 4. Roadmap de Migração

### Fase 1: Preparação do Ambiente (Dia 1)

#### 1.1 Criar estrutura do novo projeto
```bash
# Criar diretório do projeto
mkdir -p /Users/holy/projetos/auto-zap/auto-atd
cd /Users/holy/projetos/auto-zap/auto-atd

# Criar novo projeto Laravel
composer create-project laravel/laravel . --no-install

# Configurar composer.json
```

#### 1.2 Configurar Docker (Laravel Sail)
- Criar `compose.yaml` na raiz
- Criar diretórios Docker: `docker/8.5/`, `docker/php/`, `docker/pgsql/`
- Copiar Dockerfiles do projeto original
- Configurar volumes e networks

**Arquivos Docker:**
- `compose.yaml` - Serviços: app (Laravel), pgsql (PostgreSQL)
- `docker/8.5/Dockerfile` - Imagem PHP 8.5
- `docker/php/php.ini` - Configurações PHP
- `docker/pgsql/create-testing-database.sql` - Database de teste

#### 1.3 Configurar dependências
```json
{
  "require": {
    "php": "^8.2",
    "laravel/framework": "^12.0",
    "filament/filament": "^3.0",
    "simplesoftwareio/simple-qrcode": "^4.2",
    "laravel/sail": "^1.51"
  }
}
```

#### 1.4 Ambiente básico
- Criar `.env` baseado em `.env.example`
- Configurar PostgreSQL como database padrão
- Configurar APP_NAME="Auto-ATD"
- Configurar APP_URL

---

### Fase 2: Estrutura de Dados (Dia 2)

#### 2.1 Migrations

**Ordem de execução:**
1. `create_users_table.php` - Usuários/Operadores
2. `create_setors_table.php` - Setores de atendimento
3. `create_configuracao_setors_table.php` - Configurações
4. `create_senhas_table.php` - Senhas

**Schema Resumido:**

```sql
-- users
id, name, email, password, setor_id, created_at, updated_at

-- setors
id, nome, sigla, descricao, ativo, created_at, updated_at

-- configuracao_setors
id, setor_id (FK), contador_atual, prefixo, codigo_acesso,
permite_autoatendimento, mensagem_painel, ativo, created_at, updated_at

-- senhas
id, setor_id (FK), numero, numero_completo, nome_associado,
status (aguardando|chamando|atendida|cancelada),
chamada_em, atendida_em, atendido_por, created_at, updated_at
```

#### 2.2 Seeders
- `SetorSeeder` - Setores de exemplo
- `SenhaSystemSeeder` - Configurações iniciais
- `UserSeeder` - Usuários administrativos

---

### Fase 3: Models e Services (Dia 3)

#### 3.1 Models a migrar

1. **Senha.php** - Com todos os scopes e métodos
2. **Setor.php** - Com relacionamentos
3. **ConfiguracaoSetor.php** - Com lógica de contador
4. **User.php** → Renomear para **Usuario.php** (opcional)

#### 3.2 Services

Migrar `SenhaService.php` com métodos:
- `criarSenha()`
- `chamarProximaSenha()`
- `senhaAtualSetor()`
- `quantidadeAguardando()`
- `estatisticasSetor()`
- `reiniciarContador()`
- `cancelarSenha()`
- `atenderSenha()`
- `configurarSetor()`
- `gerarNovoCodigoAcesso()`

---

### Fase 4: Controllers (Dia 4)

#### 4.1 Controllers a migrar

| Controller | Prefixo de Rota |
|------------|-----------------|
| `PainelSenhaController` | `/painel` |
| `AssociadoSenhaController` | `/senha` |
| `OperadorSenhaController` | `/operador` |

#### 4.2 Rotas

```php
// web.php
Route::prefix('painel')->name('painel.')->group(function () {
    Route::get('/', [PainelSenhaController::class, 'index'])->name('index');
    Route::get('/{setor}', [PainelSenhaController::class, 'setor'])->name('setor');
    Route::get('/{setor}/stream', [PainelSenhaController::class, 'stream'])->name('stream');
    Route::get('/{setor}/dados', [PainelSenhaController::class, 'dados'])->name('dados');
});

Route::prefix('senha')->name('senha.')->group(function () {
    Route::get('/{setor}/qrcode', [AssociadoSenhaController::class, 'qrcode'])->name('qrcode');
    Route::get('/{setor}/criar', [AssociadoSenhaController::class, 'formulario'])->name('formulario');
    Route::get('/{setor}', [AssociadoSenhaController::class, 'formulario'])->name('create');
    Route::post('/{setor}', [AssociadoSenhaController::class, 'store'])->name('store');
    Route::get('/comprovante/{senha}', [AssociadoSenhaController::class, 'comprovante'])->name('comprovante');
});

Route::prefix('operador')->name('operador.')->group(function () {
    Route::get('/', [OperadorSenhaController::class, 'index'])->name('index');
    Route::get('/{setor}', [OperadorSenhaController::class, 'painel'])->name('painel');
    Route::get('/{setor}/dados', [OperadorSenhaController::class, 'dados'])->name('dados');
    Route::post('/{setor}/chamar-proxima', [OperadorSenhaController::class, 'chamarProxima'])->name('chamar-proxima');
    Route::post('/{setor}/atender-atual', [OperadorSenhaController::class, 'atenderAtual'])->name('atender-atual');
    Route::post('/senha/{senha}/cancelar', [OperadorSenhaController::class, 'cancelar'])->name('cancelar');
});
```

---

### Fase 5: Views (Dia 5)

#### 5.1 Views a migrar

1. **resources/views/senha/**
   - `formulario.blade.php` - Formulário autoatendimento
   - `comprovante.blade.php` - Comprovante da senha
   - `qrcode.blade.php` - Display QR Code

2. **resources/views/painel/**
   - `index.blade.php` - Painel geral
   - `setor.blade.php` - Painel por setor (com SSE)

3. **resources/views/operador/**
   - `painel.blade.php` - Controle do operador

#### 5.2 Dependências Frontend

- TailwindCSS (via CDN ou Vite)
- Alpine.js (para interatividade)
- SimpleQRCode (para geração de QR)

---

### Fase 6: Filament Admin (Dia 6)

⚠️ **IMPORTANTE: Preservar Layout e Personalização do Filament**

#### 6.1 Layout e Personalização do Filament (A Migrar)

**Configurações do Panel Provider:**
```php
// app/Providers/Filament/AdminPanelProvider.php
- Brand nome e logo
- Tema (dark mode)
- Middleware personalizados
- Auth configurações
- Sidebar customizations
- Navigation groups
```

**Arquivos de Configuração Filament:**
- `config/filament.php` - Configurações gerais
- `app/Filament/Resources/` - Todos resources
- `app/Filament/Pages/` - Páginas customizadas
- `app/Filament/Widgets/` - Widgets customizados
- `app/Providers/Filament/AdminPanelProvider.php` - Configurações do panel
- `public/css/cabemce-theme.css` → `auto-atd-theme.css` - Tema customizado (ADAPTAR para Auto-ATD)
- `public/images/logo-cabemce.png` → `logo.png` - Logo do sistema (ADAPTAR para Auto-ATD)

**Customização CSS (cabemce-theme.css - ADAPTAR para auto-atd-theme.css):**
- Variáveis CSS (blue, red, gold)
- Logo na página de login
- Header com gradiente azul
- Sidebar branca com bordas
- Navegação ativa/hover
- Botões primários e de perigo
- Badges, links, inputs
- Tabs, cards, sections
- Stats widgets com bordas
- Página de login
- Header, breadcrumbs, notificações
- Paginação, tabelas, modals
- Toggle switches
- Dropdown menus

**Estrutura de Cores do Tema:**
```css
--cabemce-blue: #1e40af;   /* Azul principal */
--cabemce-red: #dc2626;    /* Vermelho */
--cabemce-gold: #d4af37;   /* Dourado (detalhes) */
```

**AdminPanelProvider - Configurações a Preservar:**

```php
// Branding (ADAPTAR para Auto-ATD)
->brandName('Auto-ATD')              // Era: 'CABEMCE FAQ'
->brandLogo(asset('images/logo.png')) // Adaptar logo
->brandLogoHeight('3rem')
->favicon(asset('images/logo.png'))

// Cores (PRESERVAR ou adaptar)
'primary' => '#1e40af' (Azul)
'danger' => '#dc2626' (Vermelho)
'success' => Color::Green
'warning' => Color::Amber

// Layout (PRESERVAR)
->sidebarCollapsibleOnDesktop()
->maxContentWidth('full')

// Render Hooks (Se usar CSS custom)
->renderHook('panels::styles.before', ...)

// Discovery (PRESERVAR)
->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
```

**Componentes Visuais a Preservar:**
- Navigation groups: "Atendimento", "Sistema de Demandas"
- Badges de contagem
- Cores e ícones consistentes
- Actions e modais customizados
- Relação de resources e ordenação

#### 6.2 Resources a Migrar (Preservando Funcionalidades)

1. **SenhaResource.php** (Mantendo todas features):
   - Listagem com colunas: numero_completo, setor.nome, nome_associado, status, timestamps
   - Filtros: setor, status, data de criação
   - Actions: Chamar, Atender, Cancelar (com modais de confirmação)
   - Badge de navegação: contagem de senhas aguardando
   - Cores de status: warning (aguardando), info (chamando), success (atendida), danger (cancelada)

2. **SetorResource.php** (Mantendo todas features):
   - Tabs: "Informações Básicas" e "Configuração de Senhas"
   - Tab de Configuração de Senhas:
     - Prefixo das Senhas (input text)
     - Permitir Autoatendimento (toggle)
     - Sistema de Senhas Ativo (toggle)
     - Mensagem do Painel (textarea)
     - Seção Código de Acesso (disabled input + contador info)
   - Actions: Gerar Novo Código, Reiniciar Contador, QR Code, Painel Público, Controle do Operador
   - ActionGroup "Acessos Rápidos" com 4 ações

3. **UserResource.php** (Se aplicável ao novo sistema):
   - Relacionamento com setor
   - Campos básicos de usuário

#### 6.3 Instalação e Configuração Filament

```bash
# Instalar Filament
php artisan filament:install --panels

# Publicar configurações
php artisan vendor:publish --tag=filament-config

# Criar resources mantendo estrutura
php artisan make:filament-resource Senha --generate
php artisan make:filament-resource Setor --generate
```

#### 6.4 Migração de Assets (CSS e Logo)

**Assets a criar/copiar:**

1. **Logo do sistema:**
   ```bash
   # Criar diretório de imagens
   mkdir -p public/images

   # Copiar/adaptar logo
   cp /path/to/faq-cabemce/public/images/logo-cabemce.png \
      /path/to/auto-atd/public/images/logo.png
   ```

2. **CSS customizado:**
   ```bash
   # Criar diretório CSS
   mkdir -p public/css

   # Copiar e renomear tema
   cp /path/to/faq-cabemce/public/css/cabemce-theme.css \
      /path/to/auto-atd/public/css/auto-atd-theme.css
   ```

3. **Atualizar AdminPanelProvider para usar novo CSS:**
   ```php
   // Antes:
   ->renderHook('panels::styles.before',
       fn () => '<link rel="stylesheet" href="' . asset('css/cabemce-theme.css') . '">')

   // Depois:
   ->renderHook('panels::styles.before',
       fn () => '<link rel="stylesheet" href="' . asset('css/auto-atd-theme.css') . '">')
   ```

#### 6.5 Migração de Customizações de Layout

**Arquivos a copiar/ajustar do projeto original:**

1. **Panel Provider:**
   ```bash
   # Copiar configurações de layout
   cp /path/to/faq-cabemce/app/Providers/Filament/AdminPanelProvider.php \
      /path/to/auto-atd/app/Providers/Filament/AdminPanelProvider.php
   ```

2. **Configurações:**
   ```bash
   # Filament config
   cp /path/to/faq-cabemce/config/filament.php \
      /path/to/auto-atd/config/filament.php
   ```

3. **Widgets:**
   - `SenhaStatsWidget.php` - Widget de estatísticas de senhas
     - 4 Stats: Aguardando, Chamando, Atendidas Hoje, Total Hoje
     - Cores: warning, info, success, primary
     - Ícones: heroicon-m-clock, heroicon-m-megaphone, heroicon-m-check-circle, heroicon-m-ticket
     - Sort: -1 (aparece no topo do dashboard)

4. **Personalizações de tema:**
   - Cores customizadas (se houver)
   - Branding (logo, nome)
   - CSS custom (se houver)

#### 6.5 Passo a Passo da Migração Filament

1. **Instalar Filament no novo projeto**
2. **Copiar AdminPanelProvider** do projeto original
3. **Ajustar namespaces** de `App\` para novo projeto
4. **Migrar Resources** mantendo:
   - Fields e forms
   - Columns e tables
   - Actions e modals
   - Filters
   - Badges
5. **Testar navegação e funcionalidades**
6. **Ajustar temas e cores** se necessário

---

### Fase 7: Testes e Ajustes (Dia 7)

#### 7.1 Testes funcionais

- [ ] Fluxo completo: criar senha → chamar → atender
- [ ] Autoatendimento com código de acesso
- [ ] Painel público em tempo real (SSE)
- [ ] Rate limiting de criação de senhas
- [ ] Reinício de contador
- [ ] Geração de novo código de acesso

#### 7.2 Testes de integração

- [ ] Filament admin
- [ ] PostgreSQL connection
- [ ] Docker containers

#### 7.3 Performance

- [ ] Otimização de queries (eager loading)
- [ ] Índices do banco
- [ ] Cache de configurações

---

### Fase 8: Documentação e Deploy (Dia 8)

#### 8.1 Documentação

- README.md com instruções de setup
- Documentação de API
- Guia de uso do sistema

#### 8.2 Deploy

- Configurar ambiente de produção
- Variáveis de ambiente
- Scripts de deploy

---

## 5. Docker / Laravel Sail Configuration

### compose.yaml

```yaml
services:
    laravel.test:
        build:
            context: './docker/8.5'
            dockerfile: Dockerfile
            args:
                WWWGROUP: '${WWWGROUP}'
        image: 'sail-8.5/app'
        extra_hosts:
            - 'host.docker.internal:host-gateway'
        ports:
            - '${APP_PORT:-80}:80'
            - '${VITE_PORT:-5173}:${VITE_PORT:-5173}'
        environment:
            WWWUSER: '${WWWUSER}'
            LARAVEL_SAIL: 1
            XDEBUG_MODE: '${SAIL_XDEBUG_MODE:-off}'
            XDEBUG_CONFIG: '${SAIL_XDEBUG_CONFIG:-client_host=host.docker.internal}'
            IGNITION_LOCAL_SITES_PATH: '${PWD}'
        volumes:
            - '.:/var/www/html'
        networks:
            - sail
        depends_on:
            - pgsql
    pgsql:
        image: 'postgres:18-alpine'
        ports:
            - '${FORWARD_DB_PORT:-5432}:5432'
        environment:
            PGPASSWORD: '${DB_PASSWORD:-secret}'
            POSTGRES_DB: '${DB_DATABASE}'
            POSTGRES_USER: '${DB_USERNAME}'
            POSTGRES_PASSWORD: '${DB_PASSWORD:-secret}'
        volumes:
            - 'sail-pgsql:/var/lib/postgresql'
            - './docker/pgsql/create-testing-database.sql:/docker-entrypoint-initdb.d/10-create-testing-database.sql'
        networks:
            - sail
        healthcheck:
            test:
                - CMD
                - pg_isready
                - '-q'
                - '-d'
                - '${DB_DATABASE}'
                - '-U'
                - '${DB_USERNAME}'
            retries: 3
            timeout: 5s
networks:
    sail:
        driver: bridge
volumes:
    sail-pgsql:
        driver: local
```

### .env.example

```ini
APP_NAME="Auto-ATD"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=auto_atd
DB_USERNAME=sail
DB_PASSWORD=secret

# ... outras configurações Laravel
```

---

## 6. Checklist de Migração

### Estrutura do Projeto
- [ ] Criar diretório auto-atd
- [ ] Inicializar projeto Laravel
- [ ] Configurar Sail/Docker
- [ ] Configurar composer.json

### Database
- [ ] Criar migration de setors
- [ ] Criar migration de configuracao_setors
- [ ] Criar migration de senhas
- [ ] Criar migration de users
- [ ] Criar seeders

### Backend
- [ ] Migrar model Senha
- [ ] Migrar model Setor
- [ ] Migrar model ConfiguracaoSetor
- [ ] Migrar SenhaService
- [ ] Migrar controllers
- [ ] Configurar rotas

### Frontend
- [ ] Migrar views senha/
- [ ] Migrar views painel/
- [ ] Migrar views operador/
- [ ] Ajustar assets (CSS/JS)

### Admin (Filament)
- [ ] Instalar Filament
- [ ] Copiar AdminPanelProvider do projeto original
- [ ] Copiar config/filament.php do projeto original
- [ ] Copiar e adaptar cabemce-theme.css → auto-atd-theme.css
- [ ] Copiar e adaptar logo-cabemce.png → logo.png
- [ ] Configurar brand name e logo no AdminPanelProvider
- [ ] Migrar SenhaResource (com todas actions e filtros)
- [ ] Migrar SetorResource (com tabs e configurações)
- [ ] Migrar SenhaStatsWidget (4 estatísticas)
- [ ] Preservar navigation groups e ordenação
- [ ] Preservar badges de contagem
- [ ] Preservar cores e ícones de status
- [ ] Testar navegação e layout
- [ ] Criar usuário admin

### Testes
- [ ] Testar fluxo completo
- [ ] Testar autoatendimento
- [ ] Testar painel SSE
- [ ] Testar Filament

### Documentação
- [ ] README.md
- [ ] Instruções de setup
- [ ] Documentação de uso

---

## 7. Considerações Importantes

### 7.1 Diferenças vs Projeto Original

1. **Nome do projeto**: auto-atd (dedicado)
2. **Foco**: Apenas sistema de senhas (sem FAQ, Demandas, etc.)
3. **Database**: PostgreSQL (padrão)
4. **Docker**: Arquivos na raiz do projeto

### 7.2 Melhorias Sugeridas

1. **WebSocket**: Substituir SSE por Laravel Reverb para realtime
2. **API REST**: Adicionar API para integrações futuras
3. **Relatórios**: Dashboards avançados no Filament
4. **Notificações**: Suporte a notificações visuais/sonoras no painel
5. **Multi-tenant**: Suporte a múltiplas organizações

### 7.3 Riscos e Mitigações

| Risco | Mitigação |
|-------|-----------|
| Perda de dados durante migração | Backup do banco original |
| Incompatibilidade de dependências | Version lock no composer |
| Problemas de CORS/SSE no Docker | Testes de rede early |
| Quebra de links externos | Redirecionamentos 301 |

---

## 8. Cronograma Resumido

| Dia | Atividade |
|-----|-----------|
| 1 | Setup do projeto + Docker |
| 2 | Migrations + Seeders |
| 3 | Models + Services |
| 4 | Controllers + Rotas |
| 5 | Views + Frontend |
| 6 | Filament Admin |
| 7 | Testes + Ajustes |
| 8 | Documentação + Deploy |

---

## 9. Próximos Passos

1. Revisar este roadmap
2. Aprovar arquitetura proposta
3. Iniciar Fase 1: Preparação do Ambiente

---

*Documento gerado em 2026-06-11*
*Projeto: Migração Sistema de Senhas FAQ-CABEMCE → Auto-ATD*
