<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getHistory } from '../services/serviceHistory'
import type { HistoryEntry, HistoryPageData } from '../types/typeHistory'
import { getTypeColor } from '@/features/challenge/utils/cipherStyles'
import { formatTimeHuman } from '@/features/challenge/utils/formatTime'

const entries = ref<HistoryEntry[]>([])
const page = ref(1)
const lastPage = ref(1)
const total = ref(0)
const loading = ref(true)
const error = ref(false)

function getTypeBadgeClass(name: string | null): string {
  return `type-badge type-${getTypeColor(name ?? '')}`
}

function formatDate(iso: string | null): string {
  if (!iso) return '—'
  const date = new Date(iso)
  if (Number.isNaN(date.getTime())) return '—'
  return date.toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

async function loadPage(target: number) {
  loading.value = true
  error.value = false
  try {
    const data: HistoryPageData = await getHistory(target)
    entries.value = data.data
    page.value = data.current_page
    lastPage.value = data.last_page
    total.value = data.total
  } catch {
    error.value = true
  } finally {
    loading.value = false
  }
}

onMounted(() => loadPage(1))
</script>

<template>
  <main class="history-page">
    <div class="page-inner">
      <header class="page-header">
        <h1 class="page-title">Hist&oacute;rico</h1>
        <p class="page-sub">Sua jornada pelos desafios, da primeira tentativa &agrave; consagra&ccedil;&atilde;o.</p>
      </header>

      <div v-if="loading" class="state-box">Carregando hist&oacute;rico...</div>

      <div v-else-if="error" class="state-box">
        N&atilde;o foi poss&iacute;vel carregar o hist&oacute;rico. Tente novamente mais tarde.
      </div>

      <div v-else-if="entries.length === 0" class="state-box">
        Nenhum desafio por aqui ainda. Participe de algum desafio para ele aparecer aqui.
      </div>

      <template v-else>
        <ul class="history-list">
          <li v-for="entry in entries" :key="entry.id" class="history-row">
            <div class="history-main">
              <span class="history-status" :class="entry.completed ? 'status-complete' : 'status-progress'">
                {{ entry.completed ? 'Conclu&iacute;do' : 'Em andamento' }}
              </span>
              <h3 class="history-title">{{ entry.challenge }}</h3>
              <span v-if="entry.type" :class="getTypeBadgeClass(entry.type)" class="history-type">{{ entry.type }}</span>
            </div>

            <dl class="history-meta">
              <div class="history-meta-item">
                <dt>XP</dt>
                <dd>{{ entry.xp ?? '—' }}</dd>
              </div>
              <div class="history-meta-item">
                <dt>Tempo</dt>
                <dd>{{ entry.time_taken != null ? formatTimeHuman(entry.time_taken) : '—' }}</dd>
              </div>
              <div class="history-meta-item">
                <dt>Tentativas</dt>
                <dd>{{ entry.attempts }}</dd>
              </div>
              <div class="history-meta-item">
                <dt>Dica</dt>
                <dd>{{ entry.hint_used ? 'Sim' : 'N&atilde;o' }}</dd>
              </div>
              <div v-if="entry.completed" class="history-meta-item history-meta-date">
                <dt>Conclu&iacute;do em</dt>
                <dd><time :datetime="entry.concluded_at ?? undefined">{{ formatDate(entry.concluded_at) }}</time></dd>
              </div>
            </dl>
          </li>
        </ul>

        <nav v-if="lastPage > 1" class="pagination" aria-label="Paginação do histórico">
          <button class="pagination-btn" :disabled="page <= 1" aria-label="Página anterior" @click="loadPage(page - 1)">
            &laquo; Anterior
          </button>
          <span class="pagination-info">P&aacute;gina {{ page }} de {{ lastPage }}</span>
          <button class="pagination-btn" :disabled="page >= lastPage" aria-label="Próxima página" @click="loadPage(page + 1)">
            Pr&oacute;xima &raquo;
          </button>
        </nav>
      </template>
    </div>
  </main>
</template>

<style scoped>
.history-page {
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

.history-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.history-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  padding: 16px;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  transition: border-color 0.15s ease;
}

.history-row:hover {
  border-color: var(--border-emphasis);
}

.history-main {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 6px;
  min-width: 0;
}

.history-status {
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
}

.status-complete {
  color: var(--accent-green);
  background: var(--accent-green-muted);
}

.status-progress {
  color: var(--accent-orange);
  background: var(--accent-yellow-muted);
}

.history-title {
  font-size: 15px;
  font-weight: 500;
  color: var(--text-primary);
  margin: 0;
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

.history-meta {
  display: flex;
  flex-direction: column;
  gap: 6px;
  margin: 0;
  flex-shrink: 0;
}

.history-meta-item {
  display: flex;
  align-items: baseline;
  gap: 8px;
}

.history-meta-item dt {
  font-size: 11px;
  color: var(--text-subtle);
  font-family: var(--font-mono);
}

.history-meta-item dd {
  margin: 0;
  font-size: 13px;
  color: var(--text-primary);
  font-family: var(--font-mono);
}

.history-meta-date dd {
  font-size: 12px;
  color: var(--text-secondary);
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  padding-top: 4px;
}

.pagination-btn {
  padding: 6px 14px;
  font-size: 13px;
  font-weight: 500;
  color: var(--text-primary);
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  cursor: pointer;
  font-family: var(--font-body);
  transition: background 0.15s ease, border-color 0.15s ease;
}

.pagination-btn:hover:not(:disabled) {
  background: var(--bg-emphasis);
  border-color: var(--border-emphasis);
}

.pagination-btn:disabled {
  opacity: 0.45;
  cursor: not-allowed;
}

.pagination-info {
  font-size: 13px;
  color: var(--text-secondary);
  font-family: var(--font-mono);
}

@media (max-width: 640px) {
  .history-page {
    padding: 20px 16px;
  }

  .history-row {
    flex-direction: column;
    gap: 12px;
  }
}
</style>