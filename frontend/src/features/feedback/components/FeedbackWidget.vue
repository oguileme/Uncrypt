<script setup lang="ts">
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue'
import { useAuth } from '@/features/auth/composables/useAuth'
import { submitFeedback } from '../services/serviceFeedback'
import type { FeedbackType } from '../types/typeFeedback'

type FeedbackState = 'idle' | 'submitting' | 'success' | 'error'

const { isLoggedIn, user } = useAuth()

const open = ref(false)
const selectedType = ref<FeedbackType | null>(null)
const text = ref('')
const errorMessage = ref('')
const state = ref<FeedbackState>('idle')
const panelRef = ref<HTMLElement | null>(null)
const textareaRef = ref<HTMLTextAreaElement | null>(null)

const MIN_LENGTH = 10
const MAX_LENGTH = 2000

const types: { value: FeedbackType; label: string; hint: string; color: string; muted: string }[] = [
  { value: 'bug', label: 'Bug', hint: 'Algo não está funcionando', color: 'var(--accent-red)', muted: 'var(--accent-red-muted)' },
  { value: 'feature_request', label: 'Sugestão', hint: 'Nova funcionalidade ou melhoria', color: 'var(--accent-orange)', muted: 'var(--accent-orange-muted)' },
  { value: 'general', label: 'Geral', hint: 'Outros comentários', color: 'var(--accent-blue)', muted: 'var(--accent-blue-muted)' },
]

const canSubmit = computed(
  () => selectedType.value !== null && text.value.trim().length >= MIN_LENGTH && state.value !== 'submitting',
)

function toggle() {
  open.value = !open.value
  if (open.value) {
    state.value = 'idle'
    errorMessage.value = ''
    void nextTick(() => {
      panelRef.value?.focus()
    })
  }
}

function close() {
  open.value = false
}

function selectType(type: FeedbackType) {
  selectedType.value = type
  void nextTick(() => textareaRef.value?.focus())
}

function reset() {
  selectedType.value = null
  text.value = ''
  state.value = 'idle'
  errorMessage.value = ''
}

async function handleSubmit() {
  if (!canSubmit.value || !user.value) return

  state.value = 'submitting'
  errorMessage.value = ''
  try {
    await submitFeedback({
      user_id: user.value.id,
      context_url: window.location.href,
      feedback_text: text.value.trim(),
      feedback_type: selectedType.value!,
    })
    state.value = 'success'
  } catch {
    state.value = 'error'
    errorMessage.value = 'Não foi possível enviar seu feedback. Tente novamente em instantes.'
  }
}

function handleKeydown(e: KeyboardEvent) {
  if (e.key === 'Escape' && open.value) {
    close()
  }
}

function handleClickOutside(e: MouseEvent) {
  if (open.value && panelRef.value && !panelRef.value.contains(e.target as Node)) {
    close()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <div v-if="isLoggedIn" class="feedback-widget">
    <button
      class="feedback-fab"
      aria-label="Enviar feedback"
      :aria-expanded="open"
      :title="open ? 'Fechar feedback' : 'Enviar feedback'"
      @click.stop="toggle"
    >
      <svg v-if="!open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
      </svg>
      <svg v-else width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
        <line x1="18" y1="6" x2="6" y2="18" />
        <line x1="6" y1="6" x2="18" y2="18" />
      </svg>
    </button>

    <Transition name="pop">
      <section
        v-if="open"
        ref="panelRef"
        class="feedback-panel"
        role="dialog"
        aria-modal="true"
        aria-label="Enviar feedback"
        tabindex="-1"
        @click.stop
      >
        <header class="feedback-header">
          <h2>Enviar feedback</h2>
          <p>Conte o que encontrou, o que faltou ou como podemos melhorar.</p>
        </header>

        <div v-if="state === 'success'" class="feedback-success">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--accent-green)" stroke-width="1.5" aria-hidden="true">
            <circle cx="12" cy="12" r="10" />
            <path d="m8.5 12.5 2.5 2.5 5-6" />
          </svg>
          <p>Obrigado! Recebemos seu feedback.</p>
          <button class="btn btn-primary" @click="close">Fechar</button>
        </div>

        <template v-else>
          <fieldset class="feedback-types">
            <legend>O que você quer reportar?</legend>
            <div class="feedback-type-options">
              <button
                v-for="t in types"
                :key="t.value"
                type="button"
                class="feedback-type"
                :class="{ selected: selectedType === t.value }"
                :style="selectedType === t.value ? { borderColor: t.color, backgroundColor: t.muted } : {}"
                :aria-pressed="selectedType === t.value"
                @click="selectType(t.value)"
              >
                <strong :style="{ color: t.color }">{{ t.label }}</strong>
                <span>{{ t.hint }}</span>
              </button>
            </div>
          </fieldset>

          <label class="feedback-field" :class="{ disabled: !selectedType }">
            <span class="feedback-label">
              Descrição
              <span v-if="text.length" class="feedback-counter">{{ text.length }}/{{ MAX_LENGTH }}</span>
            </span>
            <textarea
              ref="textareaRef"
              v-model="text"
              :disabled="!selectedType"
              :maxlength="MAX_LENGTH"
              rows="5"
              placeholder="Descreva o problema ou a sugestão com o máximo de detalhes…"
              :aria-label="selectedType ? 'Descrição do feedback' : 'Selecione um tipo antes de descrever'"
            ></textarea>
            <span v-if="text.trim().length > 0 && text.trim().length < MIN_LENGTH" class="feedback-hint">
              Escreva pelo menos {{ MIN_LENGTH }} caracteres ({{ text.trim().length }}/{{ MIN_LENGTH }}).
            </span>
          </label>

          <p v-if="state === 'error'" class="feedback-error" role="alert">{{ errorMessage }}</p>

          <footer class="feedback-footer">
            <button class="btn btn-outline" :disabled="state === 'submitting'" @click="reset">Limpar</button>
            <button class="btn btn-primary" :disabled="!canSubmit" @click="handleSubmit">
              <span v-if="state === 'submitting'" class="btn-spinner" aria-hidden="true"></span>
              {{ state === 'submitting' ? 'Enviando…' : 'Enviar feedback' }}
            </button>
          </footer>
        </template>
      </section>
    </Transition>
  </div>
</template>

<style scoped>
.feedback-widget {
  position: fixed;
  right: 20px;
  bottom: 20px;
  z-index: 300;
}

.feedback-fab {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 1px solid var(--border);
  background: var(--bg-overlay);
  color: var(--text-secondary);
  box-shadow: var(--shadow-float);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease, transform 0.15s ease;
}

.feedback-fab:hover {
  background: var(--bg-emphasis);
  color: var(--text-primary);
}

.feedback-fab[aria-expanded='true'] {
  transform: rotate(90deg);
}

.feedback-panel {
  position: absolute;
  right: 0;
  bottom: calc(100% + 12px);
  width: min(360px, calc(100vw - 32px));
  background: var(--bg-overlay);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-float);
  padding: 20px;
  outline: none;
}

.feedback-header h2 {
  margin: 0;
  font-size: 16px;
  color: var(--text-primary);
}

.feedback-header p {
  margin: 4px 0 16px;
  font-size: 13px;
  color: var(--text-secondary);
}

.feedback-types {
  border: none;
  margin: 0 0 14px;
  padding: 0;
}

.feedback-types legend {
  font-size: 12px;
  font-weight: 600;
  color: var(--text-subtle);
  margin-bottom: 6px;
}

.feedback-type-options {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.feedback-type {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 2px;
  padding: 8px 12px;
  border: 1px solid var(--border-muted);
  border-radius: var(--radius-sm);
  background: transparent;
  color: var(--text-primary);
  cursor: pointer;
  text-align: left;
  transition: border-color 0.15s ease, background 0.15s ease;
}

.feedback-type strong {
  font-size: 13px;
}

.feedback-type span {
  font-size: 12px;
  color: var(--text-secondary);
}

.feedback-field {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin-bottom: 12px;
}

.feedback-label {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;
  font-weight: 600;
  color: var(--text-subtle);
}

.feedback-counter {
  font-weight: 500;
  color: var(--text-subtle);
}

textarea {
  width: 100%;
  resize: vertical;
  min-height: 112px;
  padding: 10px 12px;
  font-family: var(--font-body);
  font-size: 14px;
  line-height: 1.5;
  color: var(--text-primary);
  background: var(--bg-inset);
  border: 1px solid var(--border);
  border-radius: var(--radius-sm);
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.15s ease;
}

textarea:focus {
  border-color: var(--accent-green);
}

textarea:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.feedback-hint {
  font-size: 12px;
  color: var(--accent-orange);
}

.feedback-error {
  margin: 0 0 12px;
  font-size: 13px;
  color: var(--accent-red);
}

.feedback-footer {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

.feedback-success {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  padding: 12px 0;
  text-align: center;
}

.feedback-success p {
  margin: 0;
  font-size: 14px;
  color: var(--text-primary);
}

.btn-spinner {
  width: 12px;
  height: 12px;
  border: 2px solid rgba(255, 255, 255, 0.4);
  border-top-color: #fff;
  border-radius: 50%;
  display: inline-block;
  animation: spin 0.6s linear infinite;
  margin-right: 6px;
  vertical-align: -1px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.pop-enter-active,
.pop-leave-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}

.pop-enter-from,
.pop-leave-to {
  opacity: 0;
  transform: translateY(6px);
}
</style>