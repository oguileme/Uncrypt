# Uncrypt

Plataforma de aprendizagem e prática de criptografia, do básico às cifras clássicas até modelos modernos. Os usuários evoluem seus conhecimentos através de desafios práticos e gamificados.

## Sobre o projeto

O Uncrypt é um sistema onde o usuário escolhe uma cifra, inicia um desafio que fica registrado no seu progresso, decifra a mensagem e ganha XP ao acertar. Cada desafio passa pela criptografia real da plataforma: o usuário recebe apenas o texto cifrado, gerado pelos métodos do próprio sistema.

## Tecnologias

- **Backend:** Laravel (API REST) + Sanctum (autenticação por sessão) + PostgreSQL + Redis (cache via `predis`)
- **Frontend:** Vue 3 + TypeScript + Vite + Vue Router + Axios
- **Infraestrutura:** Docker Compose (PostgreSQL 16 · Redis 7 · PHP 8.4-FPM · Node 22)

## O que já está funcionando

### Autenticação

- Cadastro, login e logout baseados em **sessão server-side** com cookie `httpOnly` (Sanctum SPA / `statefulApi`), sem token no `localStorage`
- Handshake de CSRF: `GET /sanctum/csrf-cookie` grava o cookie `XSRF-TOKEN`; o frontend envia `X-XSRF-TOKEN` nos pedidos de escrita (com retry automático quando o token expira, 419)
- Cadastro exige confirmação de senha (`password_confirmation`) e já autentica o usuário (auto-login)
- Login regenera a sessão (anti *session fixation*); logout invalida sessão e cookies
- CORS com credenciais habilitado para o domínio do SPA (`SANCTUM_STATEFUL_DOMAINS`)
- Sessão com expiração configurável (`SESSION_LIFETIME`, em minutos)
- Rotas protegidas por autenticação na API

### Segurança

- Rate limiting global em toda a API: `api` (120/min por usuário ou IP), além de `login` (5/min por IP), `register` (3/min por IP), tentativas de resolução de desafio (`attempts`, 30/min por usuário) e operações de escrita (`writes`, 60/min por usuário)
- `TRUSTED_PROXIES`: os headers `X-Forwarded-*` só são considerados de proxies explicitamente listados, mantendo o IP real do cliente nos rate limits
- Headers de segurança HTTP em todas as respostas (X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy, `Cache-Control: no-store` por padrão) via middleware `SecurityHeaders`
- Cache HTTP no browser apenas em rotas GET públicas/estáticas (`HttpCache` middleware, com ETag e respostas 304)
- Guarda de dono (IDOR) nas rotas de `challenge_user` e `achievement-progress`: o usuário só acessa os próprios registros
- Escritas em recursos compartilhados (tipos de cifra, desafios, conquistas) restritas a usuários **admin**
- `APP_DEBUG=false` por padrão em produção no Docker (nunca expõe stack traces)
- A resposta de um desafio nunca vai ao frontend — apenas o texto cifrado gerado pelos métodos de criptografia (anti-cheat)

### Desafios

- 6 tipos de cifra implementados no `CipherHelper`: Cifra de César (com chave de deslocamento), ROT13, Base64, Atbash, Morse e Vigenère (polialfabética, com palavra-chave)
- Seed com 30 desafios (5 por tipo), com XP escalonado e dicas
- Listagem agrupada por cifra com dificuldade (estrelas), XP e status do usuário (iniciar / continuar / concluído)
- Tela do desafio estilo terminal: texto cifrado, verificação da resposta no servidor, contador de tentativas, tempo de resolução e registro de dica usada
- Desafios recomendados na Home vindos de dados reais da API

### Home e métricas

- Métricas de desempenho: desafios concluídos, taxa de acerto e tempo médio por desafio (`GET /user/metrics`), agregadas em uma única query e cacheadas no Redis por 60s (invalidadas ao ganhar XP)
- Atividade recente real da Home (`GET /user/recent-activity`), com as últimas tentativas do usuário em `challenge_user` (limitadas, `attempts > 0`, mais recentes primeiro, com tempo relativo em português)

### Gamificação

- XP por desafio concluído com level up automático (curva de progressão crescente a cada nível)
- Conquistas: conteúdos/definições gerenciados por admin, com progresso individual do usuário e recompensa em XP

## Endpoints principais da API

| Método | Rota | Acesso | Descrição |
| ------ | ---- | ------ | --------- |
| GET | `/sanctum/csrf-cookie` | pública | Grava o cookie `XSRF-TOKEN` (handshake do SPA) |
| POST | `/api/register` | pública (throttle 3/min) | Cadastro com confirmação de senha e auto-login |
| POST | `/api/login` | pública (throttle 5/min) | Login via sessão (sem token) |
| POST | `/api/logout` | autenticado | Encerra a sessão e invalida cookies |
| GET | `/api/user` | autenticado | Usuário logado |
| GET | `/api/user/metrics` | autenticado | Métricas de desempenho (cache 60s) |
| GET | `/api/user/recent-activity` | autenticado | Últimas atividades em `challenge_user` (query `?limit=`, default 5, máx 20) |
| GET | `/api/type-encryption` | pública | Lista os tipos de cifra |
| POST/PUT/DELETE | `/api/type-encryption[/{id}]` | admin (throttle: writes) | Escritas de tipos de cifra |
| GET | `/api/challenges` | autenticado | Lista desafios |
| GET | `/api/challenge/recommendations` | autenticado | Desafios recomendados |
| POST/PUT/DELETE | `/api/challenges[/{id}]` | admin | Escritas de desafios |
| GET/POST/PUT/DELETE | `/api/challenge-users[/{id}]` | autenticado (dono) | Progresso do usuário nos desafios |
| POST | `/api/challenge-users/{id}/attempt` | autenticado (throttle: attempts) | Envia uma tentativa de resposta |
| GET | `/api/achievement` | autenticado | Lista conquistas (definições) |
| POST/PUT/DELETE | `/api/achievement[/{id}]` | admin | Escritas de conquistas |
| GET/POST/PUT/DELETE | `/api/achievement-progress[/{id}]` | autenticado (dono) | Progresso do usuário nas conquistas |

> Exceto rotas marcadas como públicas, todas exigem autenticação; rotas de escrita exigem o header `X-XSRF-TOKEN` (pipeline SPA). Além dos limites específicos acima, toda a API está sujeita ao teto global `api` (120/min por usuário ou IP).

## Testes

O backend usa PHPUnit com cobertura das principais garantias: auth SPA (sessão/CSRF), rate limiting, guarda de dono (IDOR), permissões de admin, métricas com cache, índices do banco e headers de segurança.

```bash
cd backend
composer test          # suíte completa (PHPUnit) — 30 testes

cd frontend
npx vue-tsc --noEmit   # checagem de tipos
npx oxlint             # lint
```

## Próximos passos

- Novos tipos de cifra (Playfair e outras)
- Histórico de desafios e reforço dos já resolvidos
- Sequência de dias (streak) e recompensas

### Infraestrutura

- Filas de processamento assíncrono (Laravel Queues + Redis)
- API Gateway com autenticação centralizada
- Pipeline de CI/CD (testes automatizados e deploy)

## Como rodar

### Com Docker (recomendado)

Pré-requisito: [Docker](https://docs.docker.com/get-docker/) com o plugin Compose.

```bash
cp .env.example .env        # credenciais do banco usadas pelo compose
docker compose up --build   # primeira execução (ou após mudar Dockerfile/dependências)
docker compose up           # execuções seguintes
```

No boot, o backend espera o healthcheck do PostgreSQL e do Redis e roda migrations + seed automaticamente.

| Serviço    | Endereço                   |
| ---------- | -------------------------- |
| Frontend   | http://localhost:5173      |
| API        | http://localhost:8000/api  |
| PostgreSQL | localhost:5433 (host)      |
| Redis      | localhost:6379 (host)      |

> A porta do Postgres é publicada em `5433` no host para não conflitar com uma instalação local na `5432`. Dentro da rede do compose o banco continua em `5432`. O Redis roda em `6379` (store de cache/sessão-métricas padrão do compose).

Comandos úteis:

```bash
docker compose logs -f backend   # acompanhar logs de um serviço
docker compose down              # parar tudo (mantém os dados do banco)
docker compose down -v           # parar e APAGAR os dados do banco
```

Use `--build` quando alterar `Dockerfile`, `composer.json` ou `package.json`; mudanças de código são refletidas na hora pelos bind mounts (o frontend tem hot reload).

### Sem Docker

#### Backend

```bash
cd backend
composer install
cp .env.example .env        # configure o acesso ao PostgreSQL e ao Redis
php artisan key:generate
php artisan migrate --seed
php artisan serve           # http://localhost:8000
```

O `.env.example` usa `CACHE_STORE=redis` e `SESSION_DRIVER=database`; sem Redis local, troque para `CACHE_STORE=database` (roda `php artisan config:cache` após editar). O domínio do frontend em SPA deve ir em `SANCTUM_STATEFUL_DOMAINS` (padrão `localhost:5173`).

#### Frontend

```bash
cd frontend
npm install
npm run dev                 # http://localhost:5173
```

## Autor

Guilherme Moreira Rocha — estudante de Engenharia de Software (UNIPAMPA, Campus Alegrete)