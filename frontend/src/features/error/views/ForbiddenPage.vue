<script setup lang="ts">
import { RouterLink } from 'vue-router'
</script>

<template>
  <main class="forbidden">
    <div class="fb-inner">
      <span class="fb-code">403</span>

      <div class="lock-card">
        <div class="lock-header">
          <span class="lock-icon" aria-hidden="true">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="3" y="11" width="18" height="11" rx="2" />
              <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
          </span>
          <span class="lock-title">acesso_restrito.log</span>
        </div>
        <div class="lock-body">
          <p class="lock-title-text">Est&aacute; rea est&aacute; cifrada com uma chave que voc&ecirc; n&atilde;o possui.</p>
          <p class="lock-sub">Somente administradores podem decifrar esta &aacute;rea do sistema.</p>
        </div>
      </div>

      <p class="fb-sub">
        Se voc&ecirc; acredita que deveria ter acesso, procure um administrador.
      </p>

      <div class="fb-actions">
        <RouterLink to="/home" class="btn btn-primary">Voltar ao Home</RouterLink>
        <RouterLink to="/challenge" class="btn btn-outline">Ver desafios</RouterLink>
      </div>
    </div>
  </main>
</template>

<style scoped>
.forbidden {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 48px 24px;
}

.fb-inner {
  width: 100%;
  max-width: 520px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  animation: fadeIn 0.5s var(--ease-out);
}

.fb-code {
  font-family: var(--font-mono);
  font-size: clamp(72px, 18vw, 128px);
  font-weight: 700;
  line-height: 1;
  letter-spacing: 0.06em;
  background: linear-gradient(135deg, var(--accent-purple), var(--accent-red));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  user-select: none;
}

.lock-card {
  width: 100%;
  margin-top: 28px;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
  background: var(--bg-inset);
  text-align: left;
}

.lock-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 16px;
  background: var(--bg-surface);
  border-bottom: 1px solid var(--border-muted);
}

.lock-icon {
  display: flex;
  color: var(--accent-purple);
}

.lock-title {
  font-family: var(--font-mono);
  font-size: 12px;
  color: var(--text-subtle);
}

.lock-body {
  padding: 20px;
}

.lock-title-text {
  margin: 0 0 4px;
  font-size: 15px;
  font-weight: 600;
  color: var(--text-primary);
}

.lock-sub {
  margin: 0;
  font-size: 13px;
  color: var(--text-secondary);
}

.fb-sub {
  margin-top: 20px;
  font-size: 14px;
  color: var(--text-secondary);
}

.fb-actions {
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
  border-color: rgba(63, 185, 80, 0.35);
}

.btn-primary:hover {
  background: var(--accent-green-hover);
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
  .fb-actions {
    flex-direction: column;
    width: 100%;
  }

  .btn {
    width: 100%;
  }
}
</style>