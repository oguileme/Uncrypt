<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink, useRoute } from 'vue-router'

const route = useRoute()

const currentPath = computed(() => route.path)

const SCRAMBLE_CHARS = '#@$%&*<>/\\[]{}=+^?'
const display = ref('404')
let timer: ReturnType<typeof setInterval> | null = null

function randomChar(): string {
  return SCRAMBLE_CHARS[Math.floor(Math.random() * SCRAMBLE_CHARS.length)] ?? '#'
}

onMounted(() => {
  let ticks = 0
  const totalTicks = 12
  timer = setInterval(() => {
    ticks++
    display.value = '404'
      .split('')
      .map((ch, i) => (ticks < (i + 1) * 4 ? randomChar() : ch))
      .join('')
    if (ticks >= totalTicks && timer) {
      clearInterval(timer)
      timer = null
    }
  }, 80)
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>

<template>
  <main class="not-found">
    <div class="nf-inner">
      <span class="nf-code">{{ display }}</span>

      <div class="terminal-card">
        <div class="terminal-header">
          <div class="terminal-dots">
            <span class="dot dot-red"></span>
            <span class="dot dot-yellow"></span>
            <span class="dot dot-green"></span>
          </div>
          <span class="terminal-title">erro_404.log</span>
        </div>
        <div class="terminal-body">
          <div class="terminal-line">
            <span class="terminal-prompt">$</span>
            <span class="terminal-text">decrypt --rota "{{ currentPath }}"</span>
          </div>
          <div class="terminal-line">
            <span class="terminal-error">ERRO: mensagem ileg&iacute;vel detectada</span>
          </div>
          <div class="terminal-line">
            <span class="terminal-hint">
              A p&aacute;gina que voc&ecirc; procura n&atilde;o p&ocirc;de ser decifrada.
            </span>
            <span class="terminal-cursor">|</span>
          </div>
        </div>
      </div>

      <p class="nf-sub">
        Talvez a cifra esteja corrompida... ou o endere&ccedil;o nunca existiu.
      </p>

      <div class="nf-actions">
        <RouterLink to="/" class="btn btn-primary">Voltar ao in&iacute;cio</RouterLink>
        <RouterLink to="/challenge" class="btn btn-outline">Ver desafios</RouterLink>
      </div>
    </div>
  </main>
</template>

<style scoped>
.not-found {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 24px;
}

.nf-inner {
  width: 100%;
  max-width: 560px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  animation: fadeIn 0.5s var(--ease-out);
}

.nf-code {
  font-family: var(--font-mono);
  font-size: clamp(72px, 18vw, 128px);
  font-weight: 700;
  line-height: 1;
  letter-spacing: 0.06em;
  background: linear-gradient(135deg, var(--accent-green), var(--accent-blue));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  animation: pulse-glow 3s ease-in-out infinite;
  user-select: none;
}

.terminal-card {
  width: 100%;
  margin-top: 32px;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--bg-inset);
  text-align: left;
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
  font-size: 14px;
  line-height: 1.7;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.terminal-line {
  display: flex;
  align-items: baseline;
  gap: 8px;
  word-break: break-all;
}

.terminal-prompt {
  color: var(--accent-green);
  font-weight: 600;
  user-select: none;
}

.terminal-text {
  color: var(--text-primary);
}

.terminal-error {
  color: var(--accent-red);
}

.terminal-hint {
  color: var(--text-secondary);
}

.terminal-cursor {
  color: var(--accent-green);
  animation: typing-cursor 0.8s step-end infinite;
  font-weight: 300;
}

.nf-sub {
  margin-top: 24px;
  font-size: 14px;
  color: var(--text-secondary);
}

.nf-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 20px;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 8px 20px;
  font-size: 14px;
  font-weight: 500;
  border-radius: var(--radius-md);
  border: 1px solid transparent;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.15s ease, border-color 0.15s ease;
}

.btn-primary {
  color: var(--text-on-emphasis);
  background: var(--accent-green-dark);
  border-color: rgba(63, 185, 80, 0.4);
}

.btn-primary:hover {
  background: #2ea043;
  text-decoration: none;
}

.btn-outline {
  color: var(--text-primary);
  background: transparent;
  border-color: var(--border);
}

.btn-outline:hover {
  background: var(--bg-emphasis);
  border-color: var(--border-emphasis);
  text-decoration: none;
}

@media (max-width: 480px) {
  .nf-actions {
    flex-direction: column;
    width: 100%;
  }

  .btn {
    width: 100%;
  }
}
</style>
