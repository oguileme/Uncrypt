<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { getRanking } from '../services/serviceRanking'
import type { RankingEntry } from '../types/typeRanking'
import { useAuth } from '@/features/auth/composables/useAuth'

const { user } = useAuth()

const entries = ref<RankingEntry[]>([])
const loading = ref(true)
const error = ref(false)

function initials(name: string): string {
  const parts = name.trim().split(/\s+/).slice(0, 2)
  return parts.map((p) => p.charAt(0)).join('').toUpperCase() || '?'
}

const medalFor = computed(() => (position: number) => {
  if (position === 1) return 'var(--accent-yellow)'
  if (position === 2) return 'var(--text-secondary)'
  if (position === 3) return 'var(--accent-orange)'
  return 'var(--border)'
})

onMounted(async () => {
  try {
    entries.value = await getRanking(10)
  } catch {
    error.value = true
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <main class="ranking-page">
    <div class="page-inner">
      <header class="page-header">
        <h1 class="page-title">Ranking</h1>
        <p class="page-sub">Os cript&oacute;grafos mais avan&ccedil;ados da plataforma.</p>
      </header>

      <div v-if="loading" class="state-box">Carregando ranking...</div>

      <div v-else-if="error" class="state-box">
        N&atilde;o foi poss&iacute;vel carregar o ranking. Tente novamente mais tarde.
      </div>

      <div v-else-if="entries.length === 0" class="state-box">
        Ainda n&atilde;o h&aacute; cript&oacute;grafos para ranquear.
      </div>

      <ol v-else class="ranking-list">
        <li
          v-for="(entry, index) in entries"
          :key="entry.id"
          class="ranking-row"
          :class="{ mine: entry.id === user?.id }"
        >
          <span
            class="ranking-position"
            :style="{ color: medalFor(index + 1) }"
            :aria-label="`Posição ${index + 1}`"
          >
            {{ index + 1 }}
          </span>

          <div class="ranking-avatar">{{ initials(entry.name) }}</div>

          <div class="ranking-info">
            <span class="ranking-name">
              {{ entry.name }}
              <span v-if="entry.id === user?.id" class="ranking-you">voc&ecirc;</span>
            </span>
            <span class="ranking-username">@{{ entry.username }}</span>
          </div>

          <div class="ranking-stats">
            <span class="ranking-level">N&iacute;vel {{ entry.level }}</span>
            <span class="ranking-xp">{{ entry.xp_progress }} XP</span>
            <span v-if="entry.current_streak > 0" class="ranking-streak" :title="`${entry.current_streak} dias de streak`">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M22 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
              </svg>
              {{ entry.current_streak }}
            </span>
          </div>
        </li>
      </ol>
    </div>
  </main>
</template>

<style scoped>
.ranking-page {
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

.state-box {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 32px 20px;
  text-align: center;
  font-size: 13px;
  color: var(--text-secondary);
}

.ranking-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 8px;
  counter-reset: none;
}

.ranking-row {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 12px 16px;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  transition: border-color 0.15s ease, background 0.15s ease;
}

.ranking-row.mine {
  background: var(--accent-green-muted);
  border-color: rgba(63, 185, 80, 0.4);
}

.ranking-position {
  width: 28px;
  flex-shrink: 0;
  font-family: var(--font-mono);
  font-size: 18px;
  font-weight: 700;
  text-align: center;
}

.ranking-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--accent-green-dark), var(--accent-green));
  color: var(--text-on-emphasis);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.02em;
  flex-shrink: 0;
}

.ranking-info {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.ranking-name {
  font-size: 14px;
  font-weight: 500;
  color: var(--text-primary);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.ranking-you {
  font-size: 11px;
  font-weight: 600;
  color: var(--accent-green);
  background: var(--bg-success-subtle);
  padding: 1px 6px;
  border-radius: var(--radius-full);
  margin-left: 6px;
  vertical-align: 1px;
}

.ranking-username {
  font-size: 12px;
  color: var(--text-secondary);
  font-family: var(--font-mono);
}

.ranking-stats {
  display: flex;
  align-items: center;
  gap: 12px;
  flex-shrink: 0;
}

.ranking-level {
  font-size: 12px;
  color: var(--text-secondary);
  font-family: var(--font-mono);
}

.ranking-xp {
  font-size: 12px;
  color: var(--text-subtle);
  font-family: var(--font-mono);
}

.ranking-streak {
  display: inline-flex;
  align-items: center;
  gap: 3px;
  font-size: 12px;
  font-family: var(--font-mono);
  color: var(--accent-orange);
}

@media (max-width: 640px) {
  .ranking-page {
    padding: 20px 16px;
  }

  .ranking-stats {
    gap: 8px;
  }

  .ranking-xp {
    display: none;
  }
}
</style>