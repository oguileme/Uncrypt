<script setup lang="ts">
import { computed, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useTheme } from '@/composables/useTheme'
import { useAuth } from '@/features/auth/composables/useAuth'
import { logout } from '@/features/auth/services/authService'

const router = useRouter()
const { theme, setTheme } = useTheme()
const { user, clearAuth } = useAuth()

const loggingOut = ref(false)

const initials = computed(() => {
  const name = user.value?.name?.trim()
  if (!name) return '?'
  return name
    .split(/\s+/)
    .slice(0, 2)
    .map((part) => part.charAt(0))
    .join('')
    .toUpperCase()
})

async function handleLogout() {
  if (loggingOut.value) return
  loggingOut.value = true
  try {
    await logout()
  } catch {
    // segue o deslogamento local mesmo se a API falhar
  } finally {
    clearAuth()
    loggingOut.value = false
    router.push('/')
  }
}
</script>

<template>
  <main class="settings-page">
    <div class="page-inner">
      <header class="page-header">
        <h1 class="page-title">Configura&ccedil;&otilde;es</h1>
        <p class="page-sub">Personalize sua experi&ecirc;ncia na plataforma.</p>
      </header>

      <section class="card">
        <h2 class="card-title">Apar&ecirc;ncia</h2>
        <p class="card-desc">Define como a plataforma &eacute; exibida para voc&ecirc;.</p>

        <div class="segmented" role="group" aria-label="Tema da plataforma">
          <button
            type="button"
            class="segmented-option"
            :class="{ active: theme === 'light' }"
            :aria-pressed="theme === 'light'"
            @click="setTheme('light')"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="12" r="4" />
              <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41" />
            </svg>
            Claro
          </button>
          <button
            type="button"
            class="segmented-option"
            :class="{ active: theme === 'dark' }"
            :aria-pressed="theme === 'dark'"
            @click="setTheme('dark')"
          >
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
            </svg>
            Escuro
          </button>
        </div>
      </section>

      <section class="card">
        <h2 class="card-title">Conta</h2>
        <div class="account-row">
          <div class="account-avatar">{{ initials }}</div>
          <div class="account-info">
            <span class="account-name">{{ user?.name ?? 'Usu&aacute;rio' }}</span>
            <span class="account-username">@{{ user?.username ?? '' }} &middot; N&iacute;vel {{ user?.level ?? 1 }}</span>
          </div>
          <RouterLink to="/profile" class="btn btn-outline">Ver perfil</RouterLink>
        </div>
      </section>

      <section class="card">
        <h2 class="card-title">Sess&atilde;o</h2>
        <p class="card-desc">
          Sua sess&atilde;o &eacute; mantida em cookie <code>httpOnly</code> no lado do servidor
          (Sanctum SPA); nada de token fica no <code>localStorage</code>.
        </p>
        <button type="button" class="btn btn-danger" :disabled="loggingOut" @click="handleLogout">
          {{ loggingOut ? 'Saindo…' : 'Sair da conta' }}
        </button>
      </section>
    </div>
  </main>
</template>

<style scoped>
.settings-page {
  flex: 1;
  padding: 32px 24px;
}

.page-inner {
  max-width: 720px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 20px;
  animation: fadeIn 0.5s var(--ease-out);
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 4px;
}

.page-sub {
  font-size: 14px;
  color: var(--text-secondary);
}

.card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.card-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
  margin: 0;
}

.card-desc {
  font-size: 13px;
  color: var(--text-secondary);
  margin: 0;
  line-height: 1.5;
}

.card-desc code {
  font-family: var(--font-mono);
  color: var(--accent-green);
  background: var(--bg-emphasis);
  padding: 1px 5px;
  border-radius: var(--radius-sm);
}

.segmented {
  display: inline-flex;
  gap: 4px;
  padding: 4px;
  background: var(--bg-inset);
  border: 1px solid var(--border-muted);
  border-radius: var(--radius-md);
  align-self: flex-start;
}

.segmented-option {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 16px;
  font-size: 13px;
  font-weight: 500;
  color: var(--text-secondary);
  background: transparent;
  border: 1px solid transparent;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease;
  font-family: var(--font-body);
}

.segmented-option.active {
  background: var(--bg-overlay);
  color: var(--text-primary);
  box-shadow: var(--shadow-sm);
}

.segmented-option:hover:not(.active) {
  color: var(--text-primary);
}

.account-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 4px;
}

.account-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--accent-green-dark), var(--accent-green));
  color: var(--text-on-emphasis);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.02em;
  flex-shrink: 0;
}

.account-info {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.account-name {
  font-size: 14px;
  font-weight: 500;
  color: var(--text-primary);
}

.account-username {
  font-size: 12px;
  color: var(--text-secondary);
  font-family: var(--font-mono);
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 16px;
  font-size: 13px;
  font-weight: 500;
  border-radius: var(--radius-md);
  border: 1px solid transparent;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.15s ease, border-color 0.15s ease;
  line-height: 20px;
  font-family: var(--font-body);
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

.btn-danger {
  color: var(--accent-red);
  background: var(--accent-red-muted);
  border-color: rgba(248, 81, 73, 0.3);
  align-self: flex-start;
}

.btn-danger:hover:not(:disabled) {
  background: var(--accent-red-muted);
  border-color: var(--accent-red);
}

.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .settings-page {
    padding: 20px 16px;
  }
}
</style>