<script setup lang="ts">
import { ref } from 'vue'

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')
const showPassword = ref(false)
</script>

<template>
  <main class="auth-page">
    <div class="auth-branding">
      <div class="auth-branding-bg"></div>
      <div class="auth-branding-content">
        <img src="@/assets/uncrypt logo.png" alt="" class="auth-logo" />
        <p class="auth-tagline">
          Junte-se a comunidade de decifradores
          <br />
          e comece sua jornada criptogr&aacute;fica.
        </p>
      </div>
    </div>

    <div class="auth-form-side">
      <div class="auth-form-wrap">
        <div class="auth-form-header">
          <h1 class="auth-title">Crie sua conta</h1>
          <p class="auth-subtitle">Comece a resolver desafios em segundos</p>
        </div>

        <form class="auth-form" @submit.prevent>
          <div class="form-group">
            <label for="name" class="form-label">Nome</label>
            <input
              id="name"
              v-model="name"
              type="text"
              class="form-input"
              placeholder="Seu nome de decifrador"
              autocomplete="name"
            />
          </div>

          <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input
              id="email"
              v-model="email"
              type="email"
              class="form-input"
              placeholder="seu@email.com"
              autocomplete="email"
            />
          </div>

          <div class="form-group">
            <label for="password" class="form-label">Senha</label>
            <div class="password-wrap">
              <input
                id="password"
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                class="form-input"
                placeholder="Crie uma senha forte"
                autocomplete="new-password"
              />
              <button
                type="button"
                class="password-toggle"
                @click="showPassword = !showPassword"
                :title="showPassword ? 'Ocultar senha' : 'Mostrar senha'"
              >
                <svg
                  v-if="!showPassword"
                  width="16"
                  height="16"
                  viewBox="0 0 16 16"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z" />
                  <circle cx="8" cy="8" r="2.5" />
                </svg>
                <svg
                  v-else
                  width="16"
                  height="16"
                  viewBox="0 0 16 16"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path d="M1 8s2.5-5 7-5 7 5 7 5-2.5 5-7 5-7-5-7-5z" />
                  <line x1="2" y1="2" x2="14" y2="14" />
                </svg>
              </button>
            </div>
          </div>

          <div class="form-group">
            <label for="confirm-password" class="form-label">Confirmar Senha</label>
            <input
              id="confirm-password"
              v-model="confirmPassword"
              :type="showPassword ? 'text' : 'password'"
              class="form-input"
              placeholder="Repita a senha"
              autocomplete="new-password"
            />
          </div>

          <button type="submit" class="btn-submit">Criar Conta</button>
        </form>

        <p class="auth-switch">
          Ja tem conta?
          <RouterLink to="/login">Entre</RouterLink>
        </p>
      </div>
    </div>
  </main>
</template>

<style scoped>
.auth-page {
  display: flex;
  min-height: calc(100vh - 64px);
}

.auth-branding {
  flex: 1;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.auth-branding-bg {
  position: absolute;
  inset: 0;
  background:
    linear-gradient(135deg, var(--bg-inset) 0%, var(--bg-canvas) 50%, var(--bg-surface) 100%);
}

.auth-branding-bg::before {
  content: '';
  position: absolute;
  inset: 0;
  background-image: radial-gradient(circle, var(--border-muted) 1px, transparent 1px);
  background-size: 24px 24px;
  opacity: 0.5;
}

.auth-branding-bg::after {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at 50% 50%, rgba(163, 113, 247, 0.05) 0%, transparent 60%);
}

.auth-branding-content {
  position: relative;
  z-index: 1;
  text-align: center;
  padding: 40px;
}

.auth-logo {
  height: 72px;
  width: auto;
  margin: 0 auto 24px;
  animation: pulse-glow 3s ease-in-out infinite;
}

.auth-tagline {
  font-size: 18px;
  color: var(--text-secondary);
  line-height: 1.6;
  max-width: 320px;
}

.auth-form-side {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 24px;
  background: var(--bg-canvas);
}

.auth-form-wrap {
  width: 100%;
  max-width: 400px;
  animation: fadeIn 0.5s var(--ease-out);
}

.auth-form-header {
  margin-bottom: 32px;
}

.auth-title {
  font-size: 24px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 4px;
}

.auth-subtitle {
  font-size: 14px;
  color: var(--text-secondary);
}

.auth-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-label {
  font-size: 14px;
  font-weight: 500;
  color: var(--text-primary);
}

.form-input {
  width: 100%;
  padding: 5px 12px;
  font-size: 14px;
  font-family: var(--font-body);
  color: var(--text-primary);
  background: var(--bg-overlay);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  outline: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.form-input::placeholder {
  color: var(--text-subtle);
}

.form-input:focus {
  border-color: var(--accent-blue);
  box-shadow: var(--focus-ring);
}

.password-wrap {
  position: relative;
}

.password-wrap .form-input {
  padding-right: 40px;
}

.password-toggle {
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: var(--text-subtle);
  cursor: pointer;
  padding: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.15s ease;
}

.password-toggle:hover {
  color: var(--text-secondary);
}

.btn-submit {
  width: 100%;
  padding: 8px 16px;
  font-size: 14px;
  font-weight: 600;
  font-family: var(--font-body);
  color: var(--text-on-emphasis);
  background: var(--accent-green-dark);
  border: 1px solid rgba(63, 185, 80, 0.4);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: background 0.15s ease, box-shadow 0.15s ease;
}

.btn-submit:hover {
  background: #2ea043;
}

.btn-submit:active {
  background: #238636;
}

.btn-submit:focus-visible {
  outline: none;
  box-shadow: var(--focus-ring-success);
}

.auth-switch {
  margin-top: 24px;
  text-align: center;
  font-size: 14px;
  color: var(--text-secondary);
}

.auth-switch a {
  color: var(--accent-blue);
  font-weight: 500;
}

@media (max-width: 768px) {
  .auth-page {
    flex-direction: column;
  }

  .auth-branding {
    padding: 40px 24px;
    min-height: 200px;
  }

  .auth-tagline {
    font-size: 15px;
  }

  .auth-form-side {
    padding: 32px 24px 48px;
  }
}
</style>
