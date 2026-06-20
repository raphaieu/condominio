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
2. Clique em **Pagar com Pix**
3. QR Code e copia-e-cola fake serão exibidos em `/checkout/status/{order}`

## URLs para cadastrar no Meta App

Substitua `SEU_DOMINIO` pela URL de produção:

| Finalidade | URL |
|------------|-----|
| Política de privacidade | `https://SEU_DOMINIO/privacy` |
| Termos de serviço | `https://SEU_DOMINIO/terms` |
| Exclusão de dados | `https://SEU_DOMINIO/data-deletion` |
| OAuth Redirect URI | `https://SEU_DOMINIO/auth/threads/callback` |
| Deauthorize Callback | `https://SEU_DOMINIO/webhooks/meta/deauthorize` |

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
| POST | `/webhooks/meta/deauthorize` | Webhook Meta |
| GET | `/resultado` | Resultado do usuário |
| POST | `/resultado/recalcular` | Recalcula score |
| GET | `/u/{username}` | Página pública |
| GET | `/premium` | Versão premium |
| POST | `/checkout/pix` | Cria pagamento Pix |
| GET | `/checkout/status/{order}` | Status do pagamento |
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

### Variáveis opcionais (com defaults)

| Variável | Default | Descrição |
|----------|---------|-----------|
| `APP_ENV` | `production` | Ambiente |
| `APP_DEBUG` | `false` | Debug |
| `POSTGRES_DB` | `condominio_threads` | Nome do banco |
| `POSTGRES_USER` | `condominio` | Usuário do banco |
| `THREADS_MOCK` | `true` | Mock da API Threads |
| `MERCADO_PAGO_MOCK` | `true` | Mock do Pix |
| `QUEUE_CONNECTION` | `sync` | Sem worker no MVP |
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

### Comandos pós-deploy

Execute **no container `app`** após o primeiro deploy (ou via Coolify → Execute Command):

```bash
php artisan migrate --force
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

Sem Redis, Horizon, queue worker ou scheduler neste MVP.

## Licença

Projeto privado — Condominio Threads.
