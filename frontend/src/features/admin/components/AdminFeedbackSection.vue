<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { getAdminFeedbacks, updateFeedbackStatus, deleteFeedback } from '../services/serviceAdminFeedback'
import {
  FEEDBACK_STATUS_OPTIONS,
  FEEDBACK_STATUS_LABELS,
  FEEDBACK_TYPE_OPTIONS,
  FEEDBACK_TYPE_LABELS,
} from '../types/typeAdminFeedback'
import type { AdminFeedback } from '../types/typeAdminFeedback'
import type { FeedbackStatus, FeedbackType } from '@/features/feedback/types/typeFeedback'

const emit = defineEmits<{ forbidden: [] }>()

const items = ref<AdminFeedback[]>([])
const page = ref(1)
const lastPage = ref(1)
const total = ref(0)
const loading = ref(true)
const error = ref(false)
const statusFilter = ref<FeedbackStatus | ''>('')
const typeFilter = ref<FeedbackType | ''>('')
const busyId = ref<number | null>(null)

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

function isForbidden(e: unknown): boolean {
  return axios.isAxiosError(e) && e.response?.status === 403
}

async function loadPage(target: number) {
  loading.value = true
  error.value = false
  try {
    const filters =
      statusFilter.value || typeFilter.value
        ? { status: statusFilter.value || undefined, feedback_type: typeFilter.value || undefined }
        : {}
    const data = await getAdminFeedbacks(target, filters)
    items.value = data.data
    page.value = data.current_page
    lastPage.value = data.last_page
    total.value = data.total
  } catch (e) {
    if (isForbidden(e)) {
      emit('forbidden')
      return
    }
    error.value = true
  } finally {
    loading.value = false
  }
}

function clearFilters() {
  statusFilter.value = ''
  typeFilter.value = ''
  loadPage(1)
}

async function changeStatus(feedback: AdminFeedback) {
  busyId.value = feedback.id
  try {
    await updateFeedbackStatus(feedback.id, feedback.status)
  } catch (e) {
    if (isForbidden(e)) {
      emit('forbidden')
      return
    }
    await loadPage(page.value)
  } finally {
    busyId.value = null
  }
}

async function remove(feedback: AdminFeedback) {
  const confirmed = window.confirm('Excluir este feedback? A ação é registrada na auditoria.')
  if (!confirmed) return
  busyId.value = feedback.id
  try {
    await deleteFeedback(feedback.id)
    items.value = items.value.filter((f) => f.id !== feedback.id)
    total.value -= 1
  } catch (e) {
    if (isForbidden(e)) {
      emit('forbidden')
      return
    }
    error.value = true
  } finally {
    busyId.value = null
  }
}

onMounted(() => loadPage(1))
</script>

<template>
  <section class="admin-section">
    <header class="section-header">
      <div>
        <h2 class="section-title">Feedback</h2>
        <p class="section-sub">{{ total }} registro{{ total === 1 ? '' : 's' }}</p>
      </div>
      <div class="filters">
        <label class="filter-label">
          <span class="filter-name">Status</span>
          <select v-model="statusFilter" class="filter-select" @change="loadPage(1)">
            <option value="">Todos</option>
            <option v-for="s in FEEDBACK_STATUS_OPTIONS" :key="s" :value="s">
              {{ FEEDBACK_STATUS_LABELS[s] }}
            </option>
          </select>
        </label>
        <label class="filter-label">
          <span class="filter-name">Tipo</span>
          <select v-model="typeFilter" class="filter-select" @change="loadPage(1)">
            <option value="">Todos</option>
            <option v-for="t in FEEDBACK_TYPE_OPTIONS" :key="t" :value="t">
              {{ FEEDBACK_TYPE_LABELS[t] }}
            </option>
          </select>
        </label>
        <button
          v-if="statusFilter || typeFilter"
          class="btn btn-outline"
          type="button"
          @click="clearFilters"
        >
          Limpar
        </button>
      </div>
    </header>

    <div v-if="loading" class="state-box">Carregando feedback...</div>

    <div v-else-if="error" class="state-box">
      N&atilde;o foi poss&iacute;vel carregar o feedback. Tente novamente mais tarde.
    </div>

    <div v-else-if="items.length === 0" class="state-box">Nenhum feedback encontrado.</div>

    <template v-else>
      <ul class="fb-list">
        <li v-for="feedback in items" :key="feedback.id" class="fb-row" :class="{ busy: busyId === feedback.id }">
          <div class="fb-main">
            <div class="fb-meta">
              <span class="fb-user">
                {{ feedback.user?.name ?? 'Usu&aacute;rio' }}
                <span class="fb-user-handle">@{{ feedback.user?.username ?? '?' }}</span>
              </span>
              <span class="fb-type" :class="`fb-type-${feedback.feedback_type}`">
                {{ FEEDBACK_TYPE_LABELS[feedback.feedback_type] }}
              </span>
              <time class="fb-date" :datetime="feedback.created_at">{{ formatDate(feedback.created_at) }}</time>
            </div>
            <p class="fb-text">{{ feedback.feedback_text }}</p>
            <p class="fb-context">{{ feedback.context_url }}</p>
          </div>
          <div class="fb-actions">
            <select
              v-model="feedback.status"
              class="filter-select"
              :disabled="busyId === feedback.id"
              @change="changeStatus(feedback)"
              :aria-label="`Status do feedback ${feedback.id}`"
            >
              <option v-for="s in FEEDBACK_STATUS_OPTIONS" :key="s" :value="s">
                {{ FEEDBACK_STATUS_LABELS[s] }}
              </option>
            </select>
            <button
              class="btn btn-danger"
              type="button"
              :disabled="busyId === feedback.id"
              @click="remove(feedback)"
            >
              Excluir
            </button>
          </div>
        </li>
      </ul>

      <nav v-if="lastPage > 1" class="pagination" aria-label="Paginação do feedback">
        <button class="pagination-btn" :disabled="page <= 1" aria-label="Página anterior" @click="loadPage(page - 1)">
          &laquo; Anterior
        </button>
        <span class="pagination-info">P&aacute;gina {{ page }} de {{ lastPage }}</span>
        <button class="pagination-btn" :disabled="page >= lastPage" aria-label="Próxima página" @click="loadPage(page + 1)">
          Pr&oacute;xima &raquo;
        </button>
      </nav>
    </template>
  </section>
</template>

<style scoped>
.admin-section {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 20px;
  animation: fadeIn 0.4s var(--ease-out);
}

.section-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.section-title {
  margin: 0;
  font-size: 17px;
  font-weight: 600;
  color: var(--text-primary);
}

.section-sub {
  margin: 2px 0 0;
  font-size: 13px;
  color: var(--text-secondary);
}

.filters {
  display: flex;
  align-items: flex-end;
  gap: 10px;
  flex-wrap: wrap;
}

.filter-label {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.filter-name {
  font-size: 11px;
  color: var(--text-subtle);
  font-family: var(--font-mono);
}

.filter-select {
  padding: 6px 10px;
  font-size: 13px;
  color: var(--text-primary);
  background: var(--bg-inset);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  font-family: var(--font-body);
}

.state-box {
  background: var(--bg-inset);
  border: 1px dashed var(--border);
  border-radius: var(--radius-md);
  padding: 32px 20px;
  text-align: center;
  font-size: 13px;
  color: var(--text-secondary);
}

.fb-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.fb-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  padding: 14px;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  transition: border-color 0.15s ease, opacity 0.15s ease;
}

.fb-row:hover {
  border-color: var(--border-emphasis);
}

.fb-row.busy {
  opacity: 0.55;
}

.fb-main {
  min-width: 0;
}

.fb-meta {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.fb-user {
  font-size: 13px;
  font-weight: 600;
  color: var(--text-primary);
}

.fb-user-handle {
  font-weight: 400;
  color: var(--text-subtle);
}

.fb-type {
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
}

.fb-type-bug {
  color: var(--accent-red);
  background: var(--accent-red-muted);
}

.fb-type-feature_request {
  color: var(--accent-blue);
  background: var(--accent-blue-muted);
}

.fb-type-general {
  color: var(--accent-purple);
  background: var(--accent-purple-muted);
}

.fb-date {
  font-size: 12px;
  color: var(--text-subtle);
  font-family: var(--font-mono);
}

.fb-text {
  margin: 8px 0 4px;
  font-size: 14px;
  color: var(--text-primary);
  white-space: pre-wrap;
  word-break: break-word;
}

.fb-context {
  margin: 0;
  font-size: 12px;
  color: var(--text-subtle);
  font-family: var(--font-mono);
  word-break: break-all;
}

.fb-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-shrink: 0;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 14px;
  font-size: 13px;
  font-weight: 500;
  border-radius: var(--radius-md);
  border: 1px solid transparent;
  cursor: pointer;
  font-family: var(--font-body);
  transition: background 0.15s ease, border-color 0.15s ease;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-outline {
  color: var(--text-primary);
  background: transparent;
  border-color: var(--border);
}

.btn-outline:hover:not(:disabled) {
  background: var(--bg-emphasis);
  border-color: var(--border-emphasis);
}

.btn-danger {
  color: var(--accent-red);
  background: transparent;
  border-color: var(--border);
}

.btn-danger:hover:not(:disabled) {
  background: var(--accent-red-muted);
  border-color: var(--accent-red);
}

.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 16px;
  padding-top: 16px;
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
  .fb-row {
    flex-direction: column;
  }

  .fb-actions {
    width: 100%;
  }

  .fb-actions .filter-select {
    flex: 1;
  }
}
</style>