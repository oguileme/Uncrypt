# Uncrypt

Plataforma de aprendizagem e prática de criptografia, do básico às cifras clássicas até modelos modernos. Os usuários evoluem seus conhecimentos através de desafios práticos e gamificados.

## Sobre o projeto

O Uncrypt é um sistema onde o usuário escolhe uma cifra, inicia um desafio que fica registrado no seu progresso, decifra a mensagem e ganha XP ao acertar. Cada desafio passa pela criptografia real da plataforma: o usuário recebe apenas o texto cifrado, gerado pelos métodos do próprio sistema.

## Tecnologias

- **Backend:** Laravel (API REST) + Sanctum (autenticação) + PostgreSQL
- **Frontend:** Vue 3 + TypeScript + Vite + Vue Router + Axios
- **Infraestrutura:** Docker Compose (PostgreSQL 16 · PHP 8.4-FPM · Node 22)

## O que já está funcionando

### Autenticação

- Cadastro, login e logout com tokens Sanctum
- Tokens com expiração configurável (`SANCTUM_EXPIRATION`, em minutos)
- Rotas protegidas por autenticação na API

### Segurança

- Rate limiting por rota e por usuário: `login` (5/min por IP), `register` (3/min por IP), tentativas de resolução de desafio (`attempts`, 30/min por usuário) e operações de escrita (`writes`, 60/min por usuário)
- Headers de segurança HTTP em todas as respostas (X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy, Cache-Control) via middleware `SecurityHeaders`
- Guarda de dono (IDOR) nas rotas de `challenge_user`: o usuário só acessa os próprios registros
- `APP_DEBUG=false` por padrão em produção no Docker (nunca expõe stack traces)
- A resposta de um desafio nunca vai ao frontend — apenas o texto cifrado gerado pelos métodos de criptografia (anti-cheat)

### Desafios

- 6 tipos de cifra implementados no `CipherHelper`: Cifra de César (com chave de deslocamento), ROT13, Base64, Atbash, Morse e Vigenère (polialfabética, com palavra-chave)
- Seed com 30 desafios (5 por tipo), com XP escalonado e dicas
- Listagem agrupada por cifra com dificuldade (estrelas), XP e status do usuário (iniciar / continuar / concluído)
- Tela do desafio estilo terminal: texto cifrado, verificação da resposta no servidor, contador de tentativas, tempo de resolução e registro de dica usada
- Desafios recomendados na Home vindos de dados reais da API

### Home e métricas

- Métricas de desempenho: desafios concluídos, taxa de acerto e tempo médio por desafio (`GET /user/metrics`)
- Atividade recente real da Home (`GET /user/recent-activity`), com as últimas tentativas do usuário em `challenge_user` (limitadas, `attempts > 0`, mais recentes primeiro, com tempo relativo em português)

### Gamificação

- XP por desafio concluído com level up automático (curva de progressão crescente a cada nível)

## Endpoints principais da API

| Método | Rota | Acesso | Descrição |
| ------ | ---- | ------ | --------- |
| POST | `/api/register` | pública (throttle 3/min) | Cadastro |
| POST | `/api/login` | pública (throttle 5/min) | Login, retorna token Sanctum |
| POST | `/api/logout` | autenticado | Logout / revoga token |
| GET | `/api/user` | autenticado | Usuário logado |
| GET | `/api/user/metrics` | autenticado | Métricas de desempenho |
| GET | `/api/user/recent-activity` | autenticado | Últimas atividades em `challenge_user` (query `?limit=`, default 5, máx 20) |
| GET | `/api/type-encryption` | pública | Lista os tipos de cifra |
| GET | `/api/challenges` | autenticado | Lista desafios |
| GET | `/api/challenge/recommendations` | autenticado | Desafios recomendados |
| POST/PUT/DELETE | `/api/type-encryption` | autenticado (throttle: writes) | CRUD de tipos de cifra |
| POST | `/api/challenge-users/{id}/attempt` | autenticado (throttle: attempts) | Envia uma tentativa de resposta |

## Próximos passos

- Novos tipos de cifra (Playfair e outras)
- Histórico de desafios e reforço dos já resolvidos
- Conquistas, sequência de dias (streak) e recompensas
- Testes automatizados

### Infraestrutura

- Filas de processamento assíncrono (Laravel Queues + Redis)
- API Gateway com autenticação centralizada
- Cache com Redis
- Pipeline de CI/CD (testes automatizados e deploy)

## Como rodar

### Com Docker (recomendado)

Pré-requisito: [Docker](https://docs.docker.com/get-docker/) com o plugin Compose.

```bash
cp .env.example .env        # credenciais do banco usadas pelo compose
docker compose up --build   # primeira execução (ou após mudar Dockerfile/dependências)
docker compose up           # execuções seguintes
```

No boot, o backend espera o healthcheck do PostgreSQL e roda migrations + seed automaticamente.

| Serviço    | Endereço                   |
| ---------- | -------------------------- |
| Frontend   | http://localhost:5173      |
| API        | http://localhost:8000/api  |
| PostgreSQL | localhost:5433 (host)      |

> A porta do Postgres é publicada em `5433` no host para não conflitar com uma instalação local na `5432`. Dentro da rede do compose o banco continua em `5432`.

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
cp .env.example .env        # configure o acesso ao PostgreSQL
php artisan key:generate
php artisan migrate --seed
php artisan serve           # http://localhost:8000
```

#### Frontend

```bash
cd frontend
npm install
npm run dev                 # http://localhost:5173
```

## Autor

Guilherme Moreira Rocha — estudante de Engenharia de Software (UNIPAMPA, Campus Alegrete)
