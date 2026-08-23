# Uncrypt

Plataforma de aprendizagem e prática de criptografia, do básico às cifras clássicas até modelos modernos. Os usuários evoluem seus conhecimentos através de desafios práticos e gamificados.

## Sobre o projeto

O Uncrypt é um sistema onde o usuário escolhe uma cifra, inicia um desafio que fica registrado no seu progresso, decifra a mensagem e ganha XP ao acertar. Cada desafio passa pela criptografia real da plataforma: o usuário recebe apenas o texto cifrado, gerado pelos métodos do próprio sistema.

## Tecnologias

- **Backend:** Laravel (API REST) + Sanctum (autenticação) + PostgreSQL
- **Frontend:** Vue 3 + TypeScript + Vite + Vue Router + Axios

## O que já está funcionando

### Autenticação

- Cadastro, login e logout com tokens Sanctum
- Rotas protegidas por autenticação na API

### Desafios

- 3 tipos de cifra implementados no `CipherHelper`: Cifra de César (com chave de deslocamento), ROT13 e Base64
- Seed com 15 desafios (5 por tipo), com XP escalonado e dicas
- Listagem agrupada por cifra com dificuldade (estrelas), XP e status do usuário (iniciar / continuar / concluído)
- Tela do desafio estilo terminal: texto cifrado, verificação da resposta no servidor, contador de tentativas, tempo de resolução e registro de dica usada
- Anti-cheat: a resposta nunca vai ao frontend — apenas o texto cifrado gerado pelos métodos de criptografia

### Gamificação (parcial)

- XP por desafio concluído com level up automático

## Próximos passos

- Novos tipos de cifra (Vigenère, Playfair, Morse e outras)
- Conectar dados reais na Home (desafios recomendados e atividade recente hoje mockados)
- Métricas de desempenho (tempo, acertos por tipo de cifra)
- Conquistas, sequência de dias (streak) e recompensas
- Histórico de desafios e reforço dos já resolvidos
- Testes automatizados

## Como rodar

### Backend

```bash
cd backend
composer install
cp .env.example .env        # configure o acesso ao PostgreSQL
php artisan key:generate
php artisan migrate --seed
php artisan serve           # http://localhost:8000
```

### Frontend

```bash
cd frontend
npm install
npm run dev                 # http://localhost:5173
```

## Autor

Guilherme — estudante de Engenharia de Software (UNIPAMPA, Campus Alegrete)
