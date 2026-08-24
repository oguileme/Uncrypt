<script setup lang="ts">
import { ref, computed } from 'vue'
import type { UserType } from '../type/userType'

const user: UserType = {
  id: 7,
  name: 'Guilherme Rocha',
  username: 'guilherme',
  email: 'guilherme@uncrypt.dev',
  level: 4,
  xp_progress: 145,
  xp_levelup: 300,
  created_at: '2026-03-12T14:32:00.000000Z',
}

interface Achievement {
  id: number
  title: string
  description: string
  unlocked: boolean
  color: 'green' | 'blue' | 'yellow' | 'purple'
  icon: string[]
}

const achievements: Achievement[] = [
  {
    id: 1,
    title: 'Primeira Decifra',
    description: 'Resolveu seu primeiro desafio',
    unlocked: true,
    color: 'green',
    icon: [
      'M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4',
    ],
  },
  {
    id: 2,
    title: 'Mente Analítica',
    description: '5 desafios resolvidos sem usar dicas',
    unlocked: true,
    color: 'blue',
    icon: ['M13 2L3 14h9l-1 8 10-12h-9l1-8'],
  },
  {
    id: 3,
    title: 'Discípulo de César',
    description: 'Concluiu todos os desafios de Cifra de César',
    unlocked: true,
    color: 'yellow',
    icon: [
      'M23 4v6h-6M1 20v-6h6M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15',
    ],
  },
  {
    id: 4,
    title: 'Mestre do Base64',
    description: 'Conclua todos os desafios de Base64',
    unlocked: false,
    color: 'purple',
    icon: [
      'M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z',
      'M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12',
    ],
  },
  {
    id: 5,
    title: 'Invicto',
    description: '10 acertos consecutivos',
    unlocked: false,
    color: 'green',
    icon: ['M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'],
  },
]

const unlockedCount = computed(() => achievements.filter((a) => a.unlocked).length)

const emailVisible = ref(false)

const initials = computed(() => {
  const parts = user.name.trim().split(/\s+/)
  const first = parts.at(0)?.charAt(0) ?? ''
  const last = parts.length > 1 ? (parts.at(-1)?.charAt(0) ?? '') : ''
  return (first + last).toUpperCase()
})

const rankLabel = computed(() => {
  const l = user.level
  if (l <= 2) return 'Iniciante'
  if (l <= 4) return 'Criptógrafo Júnior'
  if (l <= 6) return 'Criptógrafo Pleno'
  return 'Criptógrafo Sênior'
})

const memberSince = computed(() => {
  const date = new Date(user.created_at).toLocaleDateString('pt-BR', {
    month: 'long',
    year: 'numeric',
  })
  return date.charAt(0).toUpperCase() + date.slice(1)
})

function maskPart(part?: string): string {
  if (!part) return ''
  return part.charAt(0) + '•'.repeat(Math.max(part.length - 1, 3))
}

const displayedEmail = computed(() => {
  if (emailVisible.value) return user.email
  const [local, domain] = user.email.split('@')
  if (!local || !domain) return user.email
  const [dom, tld] = domain.split('.')
  return `${maskPart(local)}@${maskPart(dom)}${tld ? `.${tld}` : ''}`
})

const xpPercent = computed(() =>
  Math.min(100, Math.round((user.xp_progress / user.xp_levelup) * 100)),
)

const xpRemaining = computed(() => Math.max(0, user.xp_levelup - user.xp_progress))
</script>

<template>
  <main class="user-page">
    <div class="user-inner">
      <div class="profile-header">
        <div class="avatar-ring">
          <div class="avatar">{{ initials }}</div>
        </div>

        <div class="header-info">
          <h1 class="profile-name">{{ user.name }}</h1>
          <span class="profile-username">@{{ user.username }}</span>
          <div class="header-chips">
            <span class="chip chip-rank">{{ rankLabel }}</span>
            <span class="chip">Membro desde {{ memberSince }}</span>
          </div>
        </div>

        <div class="header-level">
          <span class="level-value">{{ user.level }}</span>
          <span class="level-label">N&iacute;vel</span>
        </div>
      </div>

      <div class="profile-grid">
        <section class="card identity-card">
          <div class="section-header">
            <h2 class="section-title">Identidade Decifrada</h2>
          </div>
          <ul class="identity-list">
            <li class="identity-row">
              <span class="identity-key">&gt; usuario:</span>
              <span class="identity-value value-green">@{{ user.username }}</span>
            </li>
            <li class="identity-row">
              <span class="identity-key">&gt; nome:</span>
              <span class="identity-value">{{ user.name }}</span>
            </li>
            <li class="identity-row">
              <span class="identity-key">&gt; email:</span>
              <span class="identity-value">{{ displayedEmail }}</span>
              <button
                class="reveal-btn"
                type="button"
                :aria-label="emailVisible ? 'Ocultar e-mail' : 'Revelar e-mail'"
                :aria-pressed="emailVisible"
                @click="emailVisible = !emailVisible"
              >
                <svg
                  v-if="!emailVisible"
                  width="14"
                  height="14"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                >
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
                <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                  <line x1="1" y1="1" x2="23" y2="23" />
                </svg>
              </button>
            </li>
            <li class="identity-row">
              <span class="identity-key">&gt; id:</span>
              <span class="identity-value">#{{ String(user.id).padStart(3, '0') }}</span>
            </li>
            <li class="identity-row">
              <span class="identity-key">&gt; status:</span>
              <span class="identity-value value-green">
                ativo<span class="cursor">_</span>
              </span>
            </li>
          </ul>
        </section>

        <div class="profile-sidebar">
          <section class="card progress-card">
            <div class="section-header">
              <h2 class="section-title">Progresso</h2>
            </div>
            <div class="progress-body">
              <div class="xp-row">
                <span class="xp-value">{{ user.xp_progress }}</span>
                <span class="xp-total">/ {{ user.xp_levelup }} XP</span>
              </div>
              <div class="progress-bar">
                <div class="progress-fill" :style="{ width: xpPercent + '%' }"></div>
              </div>
              <p class="xp-hint">
                {{
                  xpRemaining === 0
                    ? 'Nível completo!'
                    : `Faltam ${xpRemaining} XP para alcançar o nível ${user.level + 1}.`
                }}
              </p>
            </div>
          </section>

          <section class="card achievements-card">
            <div class="section-header">
              <h2 class="section-title">Conquistas</h2>
              <span class="achievements-count">{{ unlockedCount }}/{{ achievements.length }}</span>
            </div>
            <ul class="achievements-list">
              <li
                v-for="a in achievements"
                :key="a.id"
                class="achievement-item"
                :class="{ locked: !a.unlocked }"
              >
                <div class="achievement-icon" :class="`icon-${a.color}`">
                  <svg
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path v-for="(d, i) in a.icon" :key="i" :d="d" />
                  </svg>
                  <svg
                    v-if="!a.unlocked"
                    class="lock-badge"
                    width="10"
                    height="10"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                  >
                    <rect x="3" y="11" width="18" height="11" rx="2" />
                    <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                  </svg>
                </div>
                <div class="achievement-info">
                  <span class="achievement-title">{{ a.title }}</span>
                  <span class="achievement-desc">{{ a.description }}</span>
                </div>
              </li>
            </ul>
          </section>
        </div>
      </div>
    </div>
  </main>
</template>

<style scoped>
.user-page {
  flex: 1;
  padding: 32px 24px;
}

.user-inner {
  max-width: 1280px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 20px;
  animation: fadeIn 0.5s var(--ease-out);
}

.profile-header {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 28px;
  display: flex;
  align-items: center;
  gap: 24px;
}

.avatar-ring {
  padding: 3px;
  border-radius: var(--radius-full);
  background: linear-gradient(135deg, var(--accent-green), var(--accent-blue));
  flex-shrink: 0;
}

.avatar {
  width: 84px;
  height: 84px;
  border-radius: var(--radius-full);
  background: var(--bg-surface);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: var(--font-mono);
  font-size: 26px;
  font-weight: 700;
  letter-spacing: 0.04em;
}

.header-info {
  flex: 1;
  min-width: 0;
}

.profile-name {
  font-size: 22px;
  font-weight: 600;
  color: var(--text-primary);
  line-height: 1.2;
}

.profile-username {
  font-size: 14px;
  color: var(--text-secondary);
  font-family: var(--font-mono);
}

.header-chips {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 12px;
  flex-wrap: wrap;
}

.chip {
  display: inline-flex;
  align-items: center;
  padding: 3px 10px;
  font-size: 11px;
  font-weight: 500;
  font-family: var(--font-mono);
  border-radius: var(--radius-full);
  border: 1px solid var(--border);
  color: var(--text-secondary);
}

.chip-rank {
  background: var(--accent-green-muted);
  border-color: rgba(63, 185, 80, 0.4);
  color: var(--accent-green);
}

.header-level {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  padding-left: 24px;
  border-left: 1px solid var(--border-muted);
}

.level-value {
  font-size: 40px;
  font-weight: 700;
  font-family: var(--font-mono);
  line-height: 1;
  background: linear-gradient(135deg, var(--accent-green), var(--accent-blue));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.level-label {
  font-size: 12px;
  color: var(--text-subtle);
}

.profile-grid {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 20px;
  align-items: start;
}

.card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-muted);
}

.section-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.identity-list {
  list-style: none;
  padding: 8px 20px 16px;
  display: flex;
  flex-direction: column;
}

.identity-row {
  display: flex;
  align-items: baseline;
  gap: 12px;
  padding: 12px 0;
  border-bottom: 1px solid var(--border-muted);
  font-family: var(--font-mono);
  font-size: 13px;
}

.identity-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}

.identity-key {
  color: var(--text-subtle);
  flex-shrink: 0;
  width: 110px;
}

.identity-value {
  color: var(--text-primary);
  word-break: break-all;
}

.value-green {
  color: var(--accent-green);
}

.reveal-btn {
  margin-left: auto;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  background: none;
  border: 1px solid transparent;
  border-radius: var(--radius-sm);
  color: var(--text-subtle);
  cursor: pointer;
  transition: color 0.15s ease, border-color 0.15s ease;
}

.reveal-btn:hover {
  color: var(--accent-blue);
  border-color: var(--border);
}

.cursor {
  animation: typing-cursor 1s infinite;
}

.profile-sidebar {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.progress-body {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.xp-row {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.xp-value {
  font-size: 28px;
  font-weight: 700;
  font-family: var(--font-mono);
  color: var(--text-primary);
  line-height: 1;
}

.xp-total {
  font-size: 13px;
  color: var(--text-secondary);
  font-family: var(--font-mono);
}

.progress-bar {
  width: 100%;
  height: 6px;
  background: var(--bg-emphasis);
  border-radius: var(--radius-full);
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--accent-blue), var(--accent-purple));
  border-radius: var(--radius-full);
  transition: width 0.6s var(--ease-out);
}

.xp-hint {
  font-size: 12px;
  color: var(--text-subtle);
}

.achievements-count {
  font-size: 12px;
  color: var(--text-subtle);
  background: var(--bg-emphasis);
  padding: 2px 8px;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
}

.achievements-list {
  list-style: none;
  padding: 8px 0;
  display: flex;
  flex-direction: column;
}

.achievement-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 20px;
}

.achievement-icon {
  position: relative;
  width: 32px;
  height: 32px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.icon-green {
  background: var(--accent-green-muted);
  color: var(--accent-green);
}

.icon-blue {
  background: var(--accent-blue-muted);
  color: var(--accent-blue);
}

.icon-yellow {
  background: var(--accent-yellow-muted);
  color: var(--accent-orange);
}

.icon-purple {
  background: var(--accent-purple-muted);
  color: var(--accent-purple);
}

.lock-badge {
  position: absolute;
  bottom: -3px;
  right: -3px;
  background: var(--bg-overlay);
  border: 1px solid var(--border);
  border-radius: var(--radius-full);
  padding: 2px;
  color: var(--text-subtle);
}

.achievement-info {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}

.achievement-title {
  font-size: 13px;
  font-weight: 500;
  color: var(--text-primary);
}

.achievement-desc {
  font-size: 12px;
  color: var(--text-subtle);
}

.achievement-item.locked .achievement-icon {
  background: var(--bg-emphasis);
  color: var(--text-subtle);
  opacity: 0.7;
}

.achievement-item.locked .achievement-title {
  color: var(--text-secondary);
}

@media (max-width: 1024px) {
  .profile-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .user-page {
    padding: 20px 16px;
  }

  .profile-header {
    flex-direction: column;
    text-align: center;
    padding: 24px;
  }

  .header-chips {
    justify-content: center;
  }

  .header-level {
    padding-left: 0;
    border-left: none;
    padding-top: 16px;
    border-top: 1px solid var(--border-muted);
  }
}
</style>
