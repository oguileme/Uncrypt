<script setup lang="ts">
import { ref, computed } from 'vue'

interface Challenge {
  id: number
  type: string
  typeColor: string
  difficulty: number
  title: string
  description: string
  ciphertext: string
  answer: string
  hint: string
}

const challenges: Challenge[] = [
  {
    id: 1,
    type: 'ROT13',
    typeColor: 'green',
    difficulty: 1,
    title: 'A Porta de Entrada',
    description:
      'ROT13 e uma cifra de substituicao simples. Cada letra e substituida pela letra 13 posicoes a frente no alfabeto. Aplicar duas vezes volta ao original.',
    ciphertext: 'pvsen frpergn',
    answer: 'cifra secreta',
    hint: 'METAA METAA. A metade do alfabeto e a resposta. Aplique duas vezes e volta ao original.',
  },
  {
    id: 2,
    type: 'Cifra de Cesar',
    typeColor: 'green',
    difficulty: 1,
    title: 'O Classico',
    description:
      'Julius Caesar usava esse metodo para proteger suas mensagens militares. Cada letra e deslocada por um numero fixo no alfabeto.',
    ciphertext: 'ijxfknt xjhwjyt',
    answer: 'desafio secreto',
    hint: 'Cada letra foi deslocada no alfabeto. Tente voltar 5 posicoes.',
  },
  {
    id: 3,
    type: 'Morse',
    typeColor: 'yellow',
    difficulty: 2,
    title: 'O Telegrafo',
    description:
      'Codigo Morse usa pontos e tracos para representar letras. Foi o padrao de comunicacao global por mais de 100 anos.',
    ciphertext: '... . --. .-. . -.. ---',
    answer: 'segredo',
    hint: 'Tres pontos, tres tracos, tres pontos. Simbolos universais de socorro podem ser a chave.',
  },
  {
    id: 4,
    type: 'Base64',
    typeColor: 'yellow',
    difficulty: 2,
    title: 'O Impostor',
    description:
      'Base64 nao e criptografia de verdade - e uma codificacao. Transforma dados binarios em texto ASCII. Pode ser revertida sem chave.',
    ciphertext: 'ZGVzY2lmcmFy',
    answer: 'descifrar',
    hint: 'Isso nao e criptografia de verdade - e uma codificacao. Procure uma ferramenta online de Base64 decode.',
  },
  {
    id: 5,
    type: 'Vigenere',
    typeColor: 'purple',
    difficulty: 3,
    title: 'O Inquebravel',
    description:
      'A Cifra de Vigenere usa uma palavra-chave para aplicar deslocamentos diferentes em cada letra. Foi considerada inquebravel por seculos.',
    ciphertext: 'evddkqz szgtltjw',
    answer: 'codigos secretos',
    hint: 'Uma palavra-chave de 5 letras repete-se sobre o texto. Cada letra da chave desloca a letra correspondente por um valor diferente.',
  },
]

const activeChallenge = ref<Challenge>(challenges[0]!)
const userAnswer = ref('')
const feedback = ref<'correct' | 'wrong' | null>(null)
const hintOpen = ref(false)
const attempts = ref(0)

const stars = computed(() => {
  return Array.from({ length: 5 }, (_, i) => i < activeChallenge.value.difficulty)
})

function selectChallenge(c: Challenge) {
  activeChallenge.value = c
  userAnswer.value = ''
  feedback.value = null
  hintOpen.value = false
  attempts.value = 0
}

function checkAnswer() {
  if (!userAnswer.value.trim()) return
  attempts.value++
  const normalized = userAnswer.value.trim().toLowerCase()
  const expected = activeChallenge.value.answer.toLowerCase()
  feedback.value = normalized === expected ? 'correct' : 'wrong'
}

function getTypeBadgeClass(color: string) {
  return `type-badge type-${color}`
}
</script>

<template>
  <main class="challenge-page">
    <aside class="challenge-sidebar">
      <div class="sidebar-header">
        <h2 class="sidebar-title">Desafios</h2>
        <span class="sidebar-count">{{ challenges.length }}</span>
      </div>
      <div class="challenge-list">
        <button
          v-for="c in challenges"
          :key="c.id"
          class="challenge-item"
          :class="{ active: activeChallenge.id === c.id }"
          @click="selectChallenge(c)"
        >
          <div class="challenge-item-left">
            <span :class="getTypeBadgeClass(c.typeColor)">{{ c.type }}</span>
            <span class="challenge-item-title">{{ c.title }}</span>
          </div>
          <div class="challenge-item-stars">
            <template v-for="(filled, i) in Array.from({ length: 5 }, (_, i) => i < c.difficulty)" :key="i">
              <svg
                width="12"
                height="12"
                viewBox="0 0 16 16"
                :fill="filled ? '#d29922' : 'none'"
                :stroke="filled ? '#d29922' : '#6e7681'"
                stroke-width="1.5"
              >
                <path d="M8 1.5l2 4 4.5.7-3.2 3.1.8 4.4L8 11.3l-4.1 2.4.8-4.4L1.5 6.2l4.5-.7z" />
              </svg>
            </template>
          </div>
        </button>
      </div>
    </aside>

    <section class="challenge-main">
      <div class="challenge-header">
        <div class="challenge-header-top">
          <span :class="getTypeBadgeClass(activeChallenge.typeColor)">{{ activeChallenge.type }}</span>
          <div class="challenge-stars">
            <template v-for="(filled, i) in stars" :key="i">
              <svg
                width="14"
                height="14"
                viewBox="0 0 16 16"
                :fill="filled ? '#d29922' : 'none'"
                :stroke="filled ? '#d29922' : '#6e7681'"
                stroke-width="1.5"
              >
                <path d="M8 1.5l2 4 4.5.7-3.2 3.1.8 4.4L8 11.3l-4.1 2.4.8-4.4L1.5 6.2l4.5-.7z" />
              </svg>
            </template>
          </div>
        </div>
        <h1 class="challenge-title">{{ activeChallenge.title }}</h1>
        <p class="challenge-desc">{{ activeChallenge.description }}</p>
      </div>

      <div class="terminal-card">
        <div class="terminal-header">
          <div class="terminal-dots">
            <span class="dot dot-red"></span>
            <span class="dot dot-yellow"></span>
            <span class="dot dot-green"></span>
          </div>
          <span class="terminal-title">mensagem_cifrada.txt</span>
        </div>
        <div class="terminal-body">
          <span class="terminal-prompt">$</span>
          <span class="terminal-text">{{ activeChallenge.ciphertext }}</span>
          <span class="terminal-cursor">|</span>
        </div>
      </div>

      <div class="answer-section">
        <label for="answer-input" class="answer-label">Sua resposta</label>
        <div class="answer-row">
          <input
            id="answer-input"
            v-model="userAnswer"
            type="text"
            class="answer-input"
            placeholder="Digite a mensagem decifrada..."
            :class="{
              'input-correct': feedback === 'correct',
              'input-wrong': feedback === 'wrong',
            }"
            @keyup.enter="checkAnswer"
          />
          <button class="btn-verify" @click="checkAnswer" :disabled="!userAnswer.trim()">
            Verificar
          </button>
        </div>

        <div v-if="feedback === 'correct'" class="feedback feedback-correct">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
            <path
              d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0Zm3.28 5.22a.75.75 0 0 0-1.06 0L6.75 8.69 5.28 7.22a.75.75 0 0 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l4-4a.75.75 0 0 0 0-1.06Z"
            />
          </svg>
          Correto! Voce decifrou a mensagem em {{ attempts }} tentativa{{ attempts > 1 ? 's' : '' }}.
        </div>

        <div v-else-if="feedback === 'wrong'" class="feedback feedback-wrong">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
            <path
              d="M2.343 13.657A8 8 0 1 1 13.657 2.343 8 8 0 0 1 2.343 13.657ZM6.03 4.97a.751.751 0 0 0-1.042.018.751.751 0 0 0-.018 1.042L6.94 8 4.97 9.97a.749.749 0 1 0 1.06 1.06L8 9.06l1.97 1.97a.749.749 0 1 0 1.06-1.06L9.06 8l1.97-1.97a.749.749 0 1 0-1.06-1.06L8 6.94 6.03 4.97Z"
            />
          </svg>
          Resposta incorreta. Tente novamente!
        </div>
      </div>

      <div class="hint-section">
        <button class="hint-toggle" @click="hintOpen = !hintOpen">
          <svg
            width="16"
            height="16"
            viewBox="0 0 16 16"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
          >
            <circle cx="8" cy="8" r="7" />
            <path d="M6 6a2 2 0 1 1 2 2c0 1-1 1.5-2 2" />
            <circle cx="8" cy="12.5" r="0.5" fill="currentColor" />
          </svg>
          {{ hintOpen ? 'Ocultar dica' : 'Precisa de uma dica?' }}
          <svg
            width="12"
            height="12"
            viewBox="0 0 12 12"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            :style="{ transform: hintOpen ? 'rotate(180deg)' : 'rotate(0deg)' }"
            class="chevron"
          >
            <path d="M3 5l3 3 3-3" />
          </svg>
        </button>
        <div v-if="hintOpen" class="hint-content">
          {{ activeChallenge.hint }}
        </div>
      </div>
    </section>
  </main>
</template>

<style scoped>
.challenge-page {
  display: flex;
  min-height: calc(100vh - 64px);
}

.challenge-sidebar {
  width: 300px;
  flex-shrink: 0;
  border-right: 1px solid var(--border-muted);
  background: var(--bg-surface);
  overflow-y: auto;
}

.sidebar-header {
  padding: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid var(--border-muted);
}

.sidebar-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.sidebar-count {
  font-size: 12px;
  color: var(--text-subtle);
  background: var(--bg-emphasis);
  padding: 2px 8px;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
}

.challenge-list {
  padding: 8px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.challenge-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 10px 12px;
  background: transparent;
  border: 1px solid transparent;
  border-radius: var(--radius-md);
  cursor: pointer;
  text-align: left;
  font-family: var(--font-body);
  transition: background 0.15s ease, border-color 0.15s ease;
}

.challenge-item:hover {
  background: var(--bg-emphasis);
}

.challenge-item.active {
  background: var(--bg-overlay);
  border-color: var(--border);
}

.challenge-item-left {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}

.challenge-item-title {
  font-size: 13px;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.challenge-item-stars {
  display: flex;
  gap: 1px;
  flex-shrink: 0;
}

.challenge-main {
  flex: 1;
  padding: 32px;
  max-width: 720px;
  overflow-y: auto;
  animation: fadeIn 0.3s var(--ease-out);
}

.challenge-header {
  margin-bottom: 28px;
}

.challenge-header-top {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 12px;
}

.type-badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 10px;
  font-size: 12px;
  font-weight: 600;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
  letter-spacing: 0.02em;
}

.type-green {
  background: var(--accent-green-muted);
  color: var(--accent-green);
  border: 1px solid rgba(35, 134, 54, 0.3);
}

.type-yellow {
  background: var(--accent-yellow-muted);
  color: var(--accent-yellow);
  border: 1px solid rgba(187, 128, 9, 0.3);
}

.type-purple {
  background: var(--accent-purple-muted);
  color: var(--accent-purple);
  border: 1px solid rgba(163, 113, 247, 0.3);
}

.challenge-stars {
  display: flex;
  gap: 2px;
}

.challenge-title {
  font-size: 24px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 8px;
}

.challenge-desc {
  font-size: 14px;
  color: var(--text-secondary);
  line-height: 1.5;
}

.terminal-card {
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
  margin-bottom: 28px;
  background: var(--bg-inset);
}

.terminal-header {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  background: var(--bg-surface);
  border-bottom: 1px solid var(--border-muted);
}

.terminal-dots {
  display: flex;
  gap: 6px;
}

.dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
}

.dot-red {
  background: #f85149;
}

.dot-yellow {
  background: #d29922;
}

.dot-green {
  background: #3fb950;
}

.terminal-title {
  font-size: 12px;
  color: var(--text-subtle);
  font-family: var(--font-mono);
}

.terminal-body {
  padding: 20px;
  font-family: var(--font-mono);
  font-size: 18px;
  line-height: 1.6;
  display: flex;
  align-items: center;
  gap: 8px;
}

.terminal-prompt {
  color: var(--accent-green);
  font-weight: 600;
  user-select: none;
}

.terminal-text {
  color: var(--text-primary);
  letter-spacing: 0.04em;
  word-break: break-all;
}

.terminal-cursor {
  color: var(--accent-green);
  animation: typing-cursor 0.8s step-end infinite;
  font-weight: 300;
}

.answer-section {
  margin-bottom: 24px;
}

.answer-label {
  display: block;
  font-size: 14px;
  font-weight: 500;
  color: var(--text-primary);
  margin-bottom: 8px;
}

.answer-row {
  display: flex;
  gap: 8px;
}

.answer-input {
  flex: 1;
  padding: 8px 12px;
  font-size: 14px;
  font-family: var(--font-mono);
  color: var(--text-primary);
  background: var(--bg-overlay);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  outline: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.answer-input::placeholder {
  color: var(--text-subtle);
  font-family: var(--font-body);
}

.answer-input:focus {
  border-color: var(--accent-blue);
  box-shadow: var(--focus-ring);
}

.input-correct {
  border-color: var(--accent-green) !important;
  box-shadow: var(--focus-ring-success) !important;
}

.input-wrong {
  border-color: var(--accent-red) !important;
  box-shadow: var(--focus-ring-danger) !important;
}

.btn-verify {
  padding: 8px 20px;
  font-size: 14px;
  font-weight: 600;
  font-family: var(--font-body);
  color: var(--text-on-emphasis);
  background: var(--accent-green-dark);
  border: 1px solid rgba(63, 185, 80, 0.4);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: background 0.15s ease;
  white-space: nowrap;
}

.btn-verify:hover:not(:disabled) {
  background: #2ea043;
}

.btn-verify:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.feedback {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 12px;
  padding: 10px 14px;
  border-radius: var(--radius-md);
  font-size: 13px;
  font-weight: 500;
}

.feedback-correct {
  background: var(--accent-green-muted);
  color: var(--accent-green);
  border: 1px solid rgba(35, 134, 54, 0.3);
}

.feedback-wrong {
  background: var(--accent-red-muted);
  color: var(--accent-red);
  border: 1px solid rgba(248, 81, 73, 0.3);
}

.hint-section {
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}

.hint-toggle {
  display: flex;
  align-items: center;
  gap: 8px;
  width: 100%;
  padding: 12px 16px;
  background: var(--bg-surface);
  border: none;
  cursor: pointer;
  color: var(--text-secondary);
  font-size: 13px;
  font-family: var(--font-body);
  transition: color 0.15s ease, background 0.15s ease;
}

.hint-toggle:hover {
  color: var(--text-primary);
  background: var(--bg-emphasis);
}

.chevron {
  margin-left: auto;
  transition: transform 0.2s ease;
}

.hint-content {
  padding: 16px;
  font-size: 13px;
  color: var(--text-secondary);
  line-height: 1.6;
  border-top: 1px solid var(--border-muted);
  background: var(--bg-canvas);
  animation: fadeIn 0.2s var(--ease-out);
}

@media (max-width: 768px) {
  .challenge-page {
    flex-direction: column;
  }

  .challenge-sidebar {
    width: 100%;
    border-right: none;
    border-bottom: 1px solid var(--border-muted);
    max-height: 200px;
  }

  .challenge-list {
    flex-direction: row;
    overflow-x: auto;
    padding: 8px;
    gap: 8px;
  }

  .challenge-item {
    flex-shrink: 0;
    min-width: 180px;
  }

  .challenge-main {
    padding: 20px 16px;
  }

  .terminal-body {
    font-size: 14px;
    overflow-x: auto;
  }

  .answer-row {
    flex-direction: column;
  }
}
</style>
