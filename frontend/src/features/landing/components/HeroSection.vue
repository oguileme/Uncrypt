<script setup lang="ts">
import { ref, onMounted } from 'vue'

const title = 'Desvende os Segredos da Criptografia'
const displayTitle = ref('')
const scrambleChars = '!@#$%^&*()_+-=[]{}|;:,.<>?/~`ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'

onMounted(() => {
  let iteration = 0
  const maxIterations = title.length * 2

  const interval = setInterval(() => {
    displayTitle.value = title
      .split('')
      .map((char, index) => {
        if (char === ' ') return ' '
        if (index < iteration / 2) return char
        return scrambleChars[Math.floor(Math.random() * scrambleChars.length)]
      })
      .join('')

    iteration++
    if (iteration > maxIterations) {
      displayTitle.value = title
      clearInterval(interval)
    }
  }, 35)
})
</script>

<template>
  <section class="hero">
    <div class="hero-bg"></div>
    <div class="hero-content">
      <div class="hero-logo-wrap">
        <img src="@/assets/uncrypt logo.png" alt="" class="hero-logo" />
      </div>

      <h1 class="hero-title">
        <span class="scramble">{{ displayTitle }}</span>
        <span class="cursor">|</span>
      </h1>

      <p class="hero-subtitle">
        Aprenda criptografia resolvendo desafios reais. Do ROT13 ao Vigen&egrave;re,
        suba de n&iacute;vel e desbloqueie conquistas enquanto domina as artes dos c&oacute;digos secretos.
      </p>

      <div class="hero-actions">
        <RouterLink to="/challenge" class="btn-hero-primary">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <path
              d="M8 0.5L10.3 5.2L15.5 5.9L11.7 9.5L12.6 14.7L8 12.3L3.4 14.7L4.3 9.5L0.5 5.9L5.7 5.2L8 0.5Z"
              fill="currentColor"
            />
          </svg>
          Come&ccedil;ar Agora
        </RouterLink>
        <RouterLink to="/login" class="btn-hero-secondary">Ja tenho conta</RouterLink>
      </div>

      <div class="hero-stats">
        <div class="stat">
          <span class="stat-number">5</span>
          <span class="stat-label">Tipos de Cifra</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat">
          <span class="stat-number">50+</span>
          <span class="stat-label">Desafios</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat">
          <span class="stat-number">&infin;</span>
          <span class="stat-label">Divers&atilde;o</span>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.hero {
  position: relative;
  min-height: calc(100vh - 64px);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 50% 0%, rgba(35, 134, 54, 0.08) 0%, transparent 50%),
    radial-gradient(circle at 80% 50%, rgba(68, 147, 248, 0.05) 0%, transparent 40%),
    radial-gradient(circle at 20% 80%, rgba(163, 113, 247, 0.04) 0%, transparent 40%);
  pointer-events: none;
}

.hero-bg::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: radial-gradient(circle, var(--border-muted) 1px, transparent 1px);
  background-size: 32px 32px;
  opacity: 0.4;
}

.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
  padding: 48px 24px;
  max-width: 720px;
  animation: fadeIn 0.8s var(--ease-out);
}

.hero-logo-wrap {
  margin-bottom: 32px;
}

.hero-logo {
  height: 80px;
  width: auto;
  margin: 0 auto;
  animation: pulse-glow 3s ease-in-out infinite;
}

.hero-title {
  font-family: var(--font-body);
  font-size: 48px;
  font-weight: 700;
  line-height: 1.15;
  letter-spacing: -0.02em;
  margin-bottom: 20px;
  color: var(--text-primary);
}

.scramble {
  display: inline;
}

.cursor {
  color: var(--accent-green);
  animation: typing-cursor 0.8s step-end infinite;
  font-weight: 300;
}

.hero-subtitle {
  font-size: 18px;
  line-height: 1.6;
  color: var(--text-secondary);
  max-width: 560px;
  margin: 0 auto 36px;
}

.hero-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
  margin-bottom: 48px;
}

.btn-hero-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 28px;
  font-size: 16px;
  font-weight: 600;
  color: var(--text-on-emphasis);
  background: linear-gradient(135deg, var(--accent-green-dark), var(--accent-green));
  border: 1px solid rgba(63, 185, 80, 0.4);
  border-radius: var(--radius-md);
  text-decoration: none;
  transition: transform 0.15s ease, box-shadow 0.15s ease, filter 0.15s ease;
}

.btn-hero-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(35, 134, 54, 0.4);
  text-decoration: none;
  filter: brightness(1.1);
}

.btn-hero-primary:active {
  transform: translateY(0);
}

.btn-hero-secondary {
  display: inline-flex;
  align-items: center;
  padding: 12px 24px;
  font-size: 16px;
  font-weight: 500;
  color: var(--text-secondary);
  background: transparent;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  text-decoration: none;
  transition: color 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
}

.btn-hero-secondary:hover {
  color: var(--text-primary);
  border-color: var(--border-emphasis);
  background: var(--bg-emphasis);
  text-decoration: none;
}

.hero-stats {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 32px;
}

.stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 4px;
}

.stat-number {
  font-size: 28px;
  font-weight: 700;
  color: var(--text-primary);
  font-family: var(--font-mono);
}

.stat-label {
  font-size: 12px;
  color: var(--text-subtle);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.stat-divider {
  width: 1px;
  height: 32px;
  background: var(--border);
}

@media (max-width: 768px) {
  .hero-title {
    font-size: 32px;
  }

  .hero-subtitle {
    font-size: 16px;
  }

  .hero-actions {
    flex-direction: column;
  }

  .btn-hero-primary,
  .btn-hero-secondary {
    width: 100%;
    justify-content: center;
  }

  .hero-stats {
    gap: 20px;
  }

  .stat-number {
    font-size: 22px;
  }
}
</style>
