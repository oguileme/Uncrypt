<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue'
import { useRoute } from 'vue-router'
import { getChallengeUserById, attemptChallengeUser, setHintUsed } from '../services/serviceChallengeUser'
import type { AttemptResponse } from '../services/serviceChallengeUser'
import type { ChallengeUserType } from '../types/typeChallangeUser'
import { getTypeColor, difficultyToStars } from '../utils/cipherStyles'

const route = useRoute()

const record = ref<ChallengeUserType | null>(null)
const loading = ref(true)
const loadError = ref(false)

const userAnswer = ref('')
const feedback = ref<'correct' | 'wrong' | null>(null)
const hintOpen = ref(false)
const checking = ref(false)
const xpGained = ref<number | null>(null)
const hintReduced = ref(false)
const hintConfirmOpen = ref(false)
let hintMarked = false

const activeChallenge = computed(() => record.value?.challenge ?? null)
const typeColor = computed(() => getTypeColor(activeChallenge.value?.type_encryption?.name ?? ''))
const stars = computed(() =>
  difficultyToStars(activeChallenge.value?.type_encryption?.difficulty ?? 'easy'),
)
const attempts = computed(() => record.value?.attempts ?? 0)
const isCompleted = computed(() => record.value?.completed ?? false)
const hintUsed = computed(() => record.value?.hint_used ?? false)
const baseXpPreview = computed(() => activeChallenge.value?.xp ?? 0)
const reducedXpPreview = computed(() => Math.floor(baseXpPreview.value / 2))

function getTypeBadgeClass(color: string) {
  return `type-badge type-${color}`
}

onMounted(async () => {
  try {
    record.value = await getChallengeUserById(Number(route.params.id))
  } catch {
    loadError.value = true
  } finally {
    loading.value = false
  }
})

async function checkAnswer() {
  if (!userAnswer.value.trim() || !record.value || checking.value || isCompleted.value) return
  checking.value = true
  let response: AttemptResponse
  try {
    response = await attemptChallengeUser(record.value.id, userAnswer.value.trim())
  } catch {
    response = { message: 'Erro de conexao.', completed: false }
  } finally {
    checking.value = false
  }

  feedback.value = response.completed ? 'correct' : 'wrong'

  if (response.completed) {
    xpGained.value = response.xp_gained ?? null
    hintReduced.value = response.hint_used ?? record.value?.hint_used ?? false
    if (response.challenge_user) {
      record.value = { ...record.value, ...response.challenge_user }
    } else {
      record.value = { ...record.value, completed: true }
    }
    return
  }

  if (response.message === 'Unauthorized') return
  const fresh = await getChallengeUserById(record.value.id).catch(() => null)
  if (fresh) record.value = fresh
}

function toggleHint() {
  if (!hintOpen.value && record.value && !record.value.hint_used) {
    hintConfirmOpen.value = true
    return
  }
  hintOpen.value = !hintOpen.value
}

function confirmHint() {
  hintConfirmOpen.value = false
  hintOpen.value = true
  if (!hintMarked && record.value) {
    hintMarked = true
    setHintUsed(record.value.id).catch(() => {})
  }
}

function cancelHint() {
  hintConfirmOpen.value = false
  hintOpen.value = false
}

const confirmBtnRef = ref<HTMLButtonElement | null>(null)

function handleKeydown(e: KeyboardEvent) {
  if (e.key === 'Escape' && hintConfirmOpen.value) {
    cancelHint()
  }
}

watch(hintConfirmOpen, async (open) => {
  if (open) await nextTick()
  if (open) confirmBtnRef.value?.focus()
})

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<template>
  <main class="challenge-page">
    <section v-if="loading" class="state-box">Carregando desafio...</section>

    <section v-else-if="loadError || !activeChallenge" class="state-box">
      Nao foi possivel carregar este desafio.
    </section>

    <section v-else class="challenge-main">
      <div class="challenge-header">
        <div class="challenge-header-top">
          <span :class="getTypeBadgeClass(typeColor)">{{ activeChallenge.type_encryption?.name }}</span>
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
            :disabled="isCompleted"
            :class="{
              'input-correct': feedback === 'correct',
              'input-wrong': feedback === 'wrong',
            }"
            @keyup.enter="checkAnswer"
          />
          <button
            class="btn-verify"
            :disabled="!userAnswer.trim() || checking || isCompleted"
            @click="checkAnswer"
          >
            {{ checking ? 'Verificando...' : 'Verificar' }}
          </button>
        </div>

        <div v-if="feedback === 'correct'" class="feedback feedback-correct">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
            <path
              d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0Zm3.28 5.22a.75.75 0 0 0-1.06 0L6.75 8.69 5.28 7.22a.75.75 0 0 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l4-4a.75.75 0 0 0 0-1.06Z"
            />
          </svg>
          Correto! Voce decifrou a mensagem em {{ attempts }} tentativa{{ attempts > 1 ? 's' : '' }}.
          <span v-if="xpGained">
            +{{ xpGained }} XP<span v-if="hintReduced || hintUsed"> (metade - dica usada)</span>
          </span>
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
        <button class="hint-toggle" @click="toggleHint">
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

    <Transition name="modal">
      <div
        v-if="hintConfirmOpen"
        class="dialog-overlay"
        @click.self="cancelHint"
      >
        <div class="dialog-card" role="dialog" aria-modal="true" aria-labelledby="hint-dialog-title">
          <div class="terminal-header">
            <div class="terminal-dots">
              <span class="dot dot-red"></span>
              <span class="dot dot-yellow"></span>
              <span class="dot dot-green"></span>
            </div>
            <span class="terminal-title">dica_detectada.desclog</span>
          </div>

          <div class="dialog-body">
            <div class="terminal-line">
              <span class="terminal-prompt">$</span>
              <span class="terminal-text">crypto --audit dica</span>
            </div>
            <h2 id="hint-dialog-title" class="dialog-title">
              Aviso de despacho confidencial
            </h2>
            <p class="dialog-desc">
              Usar a dica reduz o XP desta miss&atilde;o pela metade. Voc&ecirc; ainda
              pode decifrar, mas a recompensa ser&aacute; menor.
            </p>

            <div class="dialog-xp">
              <div class="xp-row">
                <span class="xp-label">XP sem dica</span>
                <span class="xp-amount">{{ baseXpPreview }}</span>
              </div>
              <div class="xp-row xp-reduced">
                <span class="xp-label">XP com dica</span>
                <span class="xp-amount">{{ reducedXpPreview }}</span>
              </div>
            </div>

            <div class="dialog-actions">
              <button class="btn-cancel" @click="cancelHint">
                Continuar sem dica
              </button>
              <button class="btn-confirm" @click="confirmHint" ref="confirmBtnRef">
                Usar dica
                <span class="btn-xp">&middot; -50% XP</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </main>
</template>

<style scoped>
.challenge-page {
  display: flex;
  min-height: calc(100vh - 64px);
}

.state-box {
  flex: 1;
  padding: 48px 24px;
  text-align: center;
  font-size: 14px;
  color: var(--text-secondary);
}

.challenge-main {
  flex: 1;
  margin: 0 auto;
  padding: 32px;
  max-width: 784px;
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

.answer-input:disabled {
  opacity: 0.6;
  cursor: not-allowed;
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

/* Confirmação de dica (modal de despacho) */
.dialog-overlay {
  position: fixed;
  inset: 0;
  z-index: 300;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 24px;
  background: rgba(1, 4, 9, 0.72);
  backdrop-filter: blur(4px);
}

.dialog-card {
  width: 100%;
  max-width: 460px;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--bg-inset);
  box-shadow: var(--shadow-float);
}

.dialog-body {
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.dialog-title {
  font-size: 17px;
  font-weight: 600;
  color: var(--text-primary);
  margin-top: 4px;
}

.dialog-desc {
  font-size: 13.5px;
  line-height: 1.55;
  color: var(--text-secondary);
}

.dialog-xp {
  margin-top: 4px;
  border: 1px solid var(--border-muted);
  border-radius: var(--radius-md);
  overflow: hidden;
}

.xp-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  background: var(--bg-surface);
}

.xp-row + .xp-row {
  border-top: 1px solid var(--border-muted);
}

.xp-row.xp-reduced {
  background: var(--accent-yellow-muted);
}

.xp-label {
  font-size: 13px;
  color: var(--text-secondary);
}

.xp-amount {
  font-size: 16px;
  font-weight: 700;
  font-family: var(--font-mono);
  color: var(--text-primary);
}

.xp-reduced .xp-amount {
  color: var(--accent-yellow);
}

.dialog-actions {
  display: flex;
  gap: 10px;
  margin-top: 12px;
}

.btn-cancel,
.btn-confirm {
  flex: 1;
  padding: 9px 16px;
  font-size: 13px;
  font-weight: 600;
  font-family: var(--font-body);
  border-radius: var(--radius-md);
  border: 1px solid transparent;
  cursor: pointer;
  transition: background 0.15s ease, border-color 0.15s ease, color 0.15s ease;
}

.btn-cancel {
  color: var(--text-primary);
  background: transparent;
  border-color: var(--border);
}

.btn-cancel:hover {
  background: var(--bg-emphasis);
  border-color: var(--border-emphasis);
}

.btn-confirm {
  color: var(--text-on-emphasis);
  background: var(--accent-green-dark);
  border-color: rgba(63, 185, 80, 0.4);
}

.btn-confirm:hover {
  background: #2ea043;
}

.btn-confirm:focus-visible,
.btn-cancel:focus-visible {
  outline: none;
  box-shadow: var(--focus-ring-success);
}

.btn-xp {
  opacity: 0.85;
  font-weight: 500;
}

.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s var(--ease-out);
}

.modal-enter-active .dialog-card,
.modal-leave-active .dialog-card {
  transition: transform 0.2s var(--ease-out);
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .dialog-card,
.modal-leave-to .dialog-card {
  transform: translateY(12px) scale(0.98);
}

@media (max-width: 768px) {
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
