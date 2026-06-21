# Condominio Threads

Experiência viral/social onde o usuário conecta sua conta Threads, a aplicação coleta métricas disponíveis via API, calcula um score simbólico e gera um resultado lúdico: tipo de imóvel, bairro, endereço simbólico, classe social digital e valor estimado do imóvel.

Stack: **Laravel 13** · **Blade** · **Tailwind CSS 4** · **Alpine.js** · **SQLite/MySQL/PostgreSQL** · **Queue (`database` local / `sync` Docker)**

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- SQLite (padrão) ou MySQL/PostgreSQL

## Instalação local

```bash
git clone <repo> condominio-threads
cd condominio-threads

composer install
cp .env.example .env
php artisan key:generate

# SQLite (padrão)
touch database/database.sqlite

# Ou configure MySQL/PostgreSQL no .env

npm install
npm run build

php artisan migrate
php artisan storage:link
php artisan db:seed --class=DemoSeeder  # opcional

php artisan serve
```

Acesse: http://localhost:8000

## Variáveis de ambiente

| Variável | Descrição |
|----------|-----------|
| `APP_URL` | URL pública da aplicação |
| `THREADS_APP_ID` | App ID do Meta/Threads |
| `THREADS_APP_SECRET` | App Secret do Meta/Threads |
| `THREADS_REDIRECT_URI` | Callback OAuth (padrão: `{APP_URL}/auth/threads/callback`) |
| `THREADS_GRAPH_BASE` | Base URL da Graph API (padrão: `https://graph.threads.net/v1.0`) |
| `THREADS_MOCK` | `true` simula login e métricas fake |
| `MERCADO_PAGO_ACCESS_TOKEN` | Token de acesso Mercado Pago |
| `MERCADO_PAGO_PUBLIC_KEY` | Chave pública Mercado Pago |
| `MERCADO_PAGO_WEBHOOK_SECRET` | Secret para validar webhooks |
| `MERCADO_PAGO_MOCK` | `true` gera Pix fake para testes |
| `MERCADO_PAGO_PREMIUM_PRICE` | Preço da versão premium (padrão: 9.90) |
| `QUEUE_CONNECTION` | `database` (padrão) |
| `PREMIUM_IMAGE_TEST_MODE` | `true` libera geração sem pagamento |
| `IMAGE_PROVIDER` | `mock` (padrão) ou `openai` |
| `OPENAI_API_KEY` | Chave da API OpenAI (quando `IMAGE_PROVIDER=openai`) |
| `OPENAI_IMAGE_MODEL` | Modelo de imagem (padrão: `gpt-image-1`; alternativas: `gpt-image-1.5`, `gpt-image-1-mini`) |
| `OPENAI_IMAGE_SIZE` | Tamanho (padrão: `1024x1536`) |
| `OPENAI_IMAGE_QUALITY` | Qualidade GPT Image: `low`, `medium`, `high` (padrão: `medium`) |
| `IMAGE_GENERATION_DISK` | Disco de storage (`public` padrão) |

> **Share cards (story/feed):** requer extensão PHP `gd` para composição. Sem GD, a fachada é gerada normalmente.

## Migrations

```bash
php artisan migrate
php artisan migrate:fresh --seed  # reset + demo
```

## Testar com mock

Com `THREADS_MOCK=true` no `.env`:

1. Acesse a home e clique em **Entrar com Threads**
2. O fluxo simula OAuth, cria conta fake, snapshot e resultado
3. Você será redirecionado para `/resultado`
4. A página pública fica em `/u/{username}`

Com `MERCADO_PAGO_MOCK=true`:

1. Gere um resultado e acesse `/premium`
2. Clique em **Pagar com Pix para gerar minha casa**
3. QR Code e copia-e-cola fake serão exibidos em `/checkout/status/{order}`
4. Clique em **Confirmar pagamento (teste)** para simular o pagamento
5. Você será redirecionado para `/premium` com a geração da imagem em andamento

Com `PREMIUM_IMAGE_TEST_MODE=true`:

1. Acesse `/premium` após gerar resultado
2. Clique em **Gerar minha casa com IA** sem passar pelo Pix

Comandos úteis:

```bash
php artisan premium:unlock @usuario      # libera geração manualmente
php artisan premium:generate-image @usuario  # dispara geração (debug)
php artisan queue:work                   # processa jobs de geração
```

## OAuth real com Threads (`THREADS_MOCK=false`)

Configure no `.env`:

```env
THREADS_MOCK=false
THREADS_APP_ID=seu_app_id
THREADS_APP_SECRET=seu_app_secret
THREADS_REDIRECT_URI=https://SEU_DOMINIO/auth/threads/callback
THREADS_GRAPH_BASE=https://graph.threads.net/v1.0
```

Escopos usados: `threads_basic`, `threads_manage_insights`.

### Desenvolvimento local com Cloudflare Tunnel

A Meta **não aceita** `localhost` como `redirect_uri`. Use um túnel HTTPS com subdomínio estável:

1. Suba o Laravel:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

2. Em outro terminal, suba o túnel:

```bash
cloudflared tunnel --url http://localhost:8000
```

Para desenvolvimento contínuo, prefira um subdomínio fixo (ex.: `https://condominio-dev.raphai.eu`) em vez da URL aleatória do túnel rápido — assim você não precisa atualizar o painel Meta a cada sessão.

3. Configure o `.env` local:

```env
APP_URL=https://condominio-dev.raphai.eu
THREADS_REDIRECT_URI=https://condominio-dev.raphai.eu/auth/threads/callback
THREADS_MOCK=false
```

4. Cadastre **ambas** as URLs de callback no Meta App (produção + dev).

### Health check

Produção:

```bash
curl https://condominio.raphai.eu/health
```

Local com tunnel:

```bash
curl https://condominio-dev.raphai.eu/health
```

Resposta esperada: `{"status":"ok"}`

### Checklist de teste OAuth real

1. `THREADS_MOCK=false`
2. `THREADS_APP_ID` preenchido
3. `THREADS_APP_SECRET` preenchido
4. `THREADS_REDIRECT_URI` **idêntico** ao cadastrado na Meta
5. Callback de produção cadastrado: `https://condominio.raphai.eu/auth/threads/callback`
6. Callback dev cadastrado (se usar tunnel): `https://condominio-dev.raphai.eu/auth/threads/callback`
7. **Usuário de teste adicionado no Meta App** (obrigatório enquanto o app estiver em modo desenvolvimento):
   - Meta Developers → seu app → **App roles** → **Roles** → **Add people** → função **Tester**
   - Ou em **Use cases** → **Access the Threads API** → adicionar **Threads Testers**
   - O usuário precisa **aceitar o convite** (notificação no Threads/Instagram/Facebook)
   - Sem isso, a API retorna: *"requires the threads_basic permission... Threads testers"*
8. Clicar em **Entrar com Threads**
9. Autorizar no Threads
10. Voltar para `/resultado`
11. Confirmar dados em `threads_accounts`, `threads_profile_snapshots`, `condominium_results`

## URLs para cadastrar no Meta App

Substitua `SEU_DOMINIO` pela URL de produção:

| Finalidade | URL |
|------------|-----|
| Política de privacidade | `https://SEU_DOMINIO/privacy` |
| Termos de serviço | `https://SEU_DOMINIO/terms` |
| Exclusão de dados | `https://SEU_DOMINIO/data-deletion` |
| OAuth Redirect URI | `https://SEU_DOMINIO/auth/threads/callback` |
| Deauthorize Callback | `https://SEU_DOMINIO/webhooks/meta/deauthorize` |
| Data Deletion Callback | `https://SEU_DOMINIO/webhooks/meta/data-deletion` |

## URLs Mercado Pago

| Finalidade | URL |
|------------|-----|
| Webhook de pagamentos | `https://SEU_DOMINIO/webhooks/mercado-pago` |

## Rotas principais

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/` | Landing page |
| GET | `/privacy` | Política de privacidade |
| GET | `/terms` | Termos de serviço |
| GET | `/data-deletion` | Exclusão de dados |
| GET | `/health` | Health check JSON |
| GET | `/auth/threads/redirect` | Inicia OAuth Threads |
| GET | `/auth/threads/callback` | Callback OAuth |
| POST | `/webhooks/meta/deauthorize` | Webhook Meta (desautorização) |
| POST | `/webhooks/meta/data-deletion` | Webhook Meta (exclusão de dados) |
| GET | `/data-deletion/status/{code}` | Status da solicitação de exclusão |
| GET | `/resultado` | Resultado do usuário |
| POST | `/resultado/recalcular` | Recalcula score |
| GET | `/u/{username}` | Página pública |
| GET | `/premium` | Versão premium / geração de imagem |
| POST | `/premium/image/generate` | Inicia geração de imagem (JSON) |
| GET | `/premium/image/status` | Status da geração (JSON) |
| POST | `/premium/image/retry` | Repete geração falha (JSON) |
| POST | `/checkout/pix` | Cria pagamento Pix |
| GET | `/checkout/status/{order}` | Status do pagamento |
| POST | `/checkout/confirm/{order}` | Confirma pagamento mock (teste) |
| POST | `/webhooks/mercado-pago` | Webhook Mercado Pago |

## Estrutura de services

```
app/Services/
├── Threads/
│   ├── ThreadsOAuthService.php
│   ├── ThreadsClient.php
│   └── ThreadsMetricsCollector.php
├── Scoring/
│   ├── ProfileScoringService.php
│   └── PropertyClassifier.php
├── Billing/
│   └── MercadoPagoService.php
├── Premium/
│   └── PremiumAccessService.php
├── ImageGeneration/
│   ├── ImageGenerationService.php
│   ├── ImagePromptBuilder.php
│   ├── HouseDescriptionBuilder.php
│   ├── ShareCardGenerator.php
│   └── Providers/ (Mock, OpenAI)
└── CondominiumResultService.php
```

## Produção

```bash
composer install --optimize-autoloader --no-dev
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Configure `THREADS_MOCK=false` e `MERCADO_PAGO_MOCK=false` quando tiver credenciais reais.

## Deploy no Coolify (Docker Compose)

A aplicação inclui `docker-compose.yml` pronto para deploy no [Coolify](https://coolify.io) com **PHP 8.3 + Nginx + PHP-FPM** (container `app`, porta 80) e **PostgreSQL 16** (container `postgres`).

Não há networks customizadas — o Coolify gerencia rede e proxy reverso.

### Arquivos Docker

| Arquivo | Função |
|---------|--------|
| `Dockerfile` | Build multi-stage: npm, composer, imagem final com Nginx + PHP-FPM + Supervisor |
| `docker-compose.yml` | Serviços `app` e `postgres` |
| `docker/nginx.conf` | Virtual host Laravel (`public/`) |
| `docker/supervisord.conf` | Gerencia Nginx e PHP-FPM |
| `docker/php.ini` | OPcache e limites PHP para produção |
| `docker/entrypoint.sh` | Permissões de `storage/` e default de `THREADS_REDIRECT_URI` |
| `.dockerignore` | Contexto de build enxuto |

### Passo a passo no Coolify

1. Crie um novo recurso **Docker Compose** apontando para este repositório.
2. O Coolify detectará o `docker-compose.yml` na raiz.
3. Configure o domínio público no serviço **`app`** (porta interna **80**).
4. Defina as variáveis de ambiente obrigatórias (aba Environment).
5. Faça o deploy (build + start dos containers).
6. Execute os **comandos pós-deploy** no container `app` (ver abaixo).

### Variáveis obrigatórias

| Variável | Descrição |
|----------|-----------|
| `APP_URL` | URL pública com HTTPS (ex: `https://condominio.example.com`) |
| `APP_KEY` | Chave Laravel (`php artisan key:generate --show`) |
| `POSTGRES_PASSWORD` | Senha do banco (mesma para app e postgres) |

> **HTTPS / assets:** use `APP_URL` com `https://`. O app confia no proxy do Coolify e força HTTPS nas URLs geradas. Se CSS/JS carregarem via HTTP (mixed content), confira `APP_URL` e rode `php artisan optimize:clear && php artisan optimize` no container.

### Variáveis opcionais (com defaults)

| Variável | Default | Descrição |
|----------|---------|-----------|
| `APP_ENV` | `production` | Ambiente |
| `APP_DEBUG` | `false` | Debug |
| `POSTGRES_DB` | `condominio_threads` | Nome do banco |
| `POSTGRES_USER` | `condominio` | Usuário do banco |
| `THREADS_MOCK` | `true` | Mock da API Threads |
| `MERCADO_PAGO_MOCK` | `true` | Mock do Pix |
| `QUEUE_CONNECTION` | `database` | Fila para geração de imagem |
| `THREADS_REDIRECT_URI` | `{APP_URL}/auth/threads/callback` | Definido automaticamente no entrypoint se vazio |

### Variáveis Threads API (quando sair do mock)

| Variável | Descrição |
|----------|-----------|
| `THREADS_APP_ID` | App ID Meta/Threads |
| `THREADS_APP_SECRET` | App Secret |
| `THREADS_GRAPH_BASE` | `https://graph.threads.net/v1.0` |
| `THREADS_MOCK` | `false` |

### Variáveis Mercado Pago (quando sair do mock)

| Variável | Descrição |
|----------|-----------|
| `MERCADO_PAGO_ACCESS_TOKEN` | Token de acesso |
| `MERCADO_PAGO_PUBLIC_KEY` | Chave pública |
| `MERCADO_PAGO_WEBHOOK_SECRET` | Secret do webhook |
| `MERCADO_PAGO_PREMIUM_PRICE` | `9.90` |
| `MERCADO_PAGO_MOCK` | `false` |

### Variáveis geração de imagem

| Variável | Descrição |
|----------|-----------|
| `PREMIUM_IMAGE_TEST_MODE` | `true` libera geração sem pagamento |
| `IMAGE_PROVIDER` | `mock` ou `openai` |
| `OPENAI_API_KEY` | Chave OpenAI |
| `OPENAI_IMAGE_MODEL` | `gpt-image-1` |
| `OPENAI_IMAGE_SIZE` | `1024x1536` |
| `OPENAI_IMAGE_QUALITY` | `medium` |
| `IMAGE_GENERATION_DISK` | `public` |

### Comandos pós-deploy

Execute **no container `app`** após o primeiro deploy (ou via Coolify → Execute Command):

```bash
php artisan migrate --force
php artisan storage:link --force
php artisan optimize:clear
php artisan optimize
```

Repita `migrate --force` sempre que houver novas migrations.

### Health check

Confirme que a aplicação responde:

```bash
curl -s https://SEU_DOMINIO/health
# {"status":"ok"}
```

### Build local (teste)

```bash
export APP_KEY=base64:$(openssl rand -base64 32)
export APP_URL=http://localhost
export POSTGRES_PASSWORD=secret

docker compose up --build -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize:clear
docker compose exec app php artisan optimize
```

### O que o Dockerfile faz no build

1. `npm install` + `npm run build` (assets Vite/Tailwind)
2. `composer install --no-dev` + `composer dump-autoload --optimize`
3. Configura permissões de `storage/` e `bootstrap/cache/`
4. Inicia **Nginx** e **PHP-FPM** via **Supervisor** na porta 80

Sem Redis ou Horizon. O container Docker inclui **queue worker** via Supervisor para processar geração de imagem.

## Licença

Projeto privado — Condominio Threads.
