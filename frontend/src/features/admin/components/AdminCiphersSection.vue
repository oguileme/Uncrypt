<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { getTypeEncryptions, deleteTypeEncryption } from '@/features/challenge/services/serviceTypeEncryption'
import type { TypeEncryptionType } from '@/features/challenge/types/typeTypeEncryption'

const emit = defineEmits<{ forbidden: [] }>()

const items = ref<TypeEncryptionType[]>([])
const loading = ref(true)
const error = ref(false)
const busyId = ref<number | null>(null)

function isForbidden(e: unknown): boolean {
  return axios.isAxiosError(e) && e.response?.status === 403
}

async function loadAll() {
  loading.value = true
  error.value = false
  try {
    items.value = await getTypeEncryptions()
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

async function remove(cipher: TypeEncryptionType) {
  const confirmed = window.confirm(`Excluir cifra "${cipher.name}"? Os desafios que usam esta cifra serão removidos.`)
  if (!confirmed) return
  busyId.value = cipher.id
  try {
    await deleteTypeEncryption(cipher.id)
    items.value = items.value.filter((c) => c.id !== cipher.id)
  } catch (e) {
    if (isForbidden(e)) {
      emit('forbidden')
      return
    }
  } finally {
    busyId.value = null
  }
}

onMounted(() => loadAll())
</script>

<template>
  <section class="admin-section">
    <header class="section-header">
      <div>
        <h2 class="section-title">Cifras</h2>
        <p class="section-sub">{{ items.length }} registro{{ items.length === 1 ? '' : 's' }}</p>
      </div>
    </header>

    <div v-if="loading" class="state-box">Carregando cifras...</div>

    <div v-else-if="error" class="state-box">N&atilde;o foi poss&iacute;vel carregar as cifras.</div>

    <div v-else-if="items.length === 0" class="state-box">Nenhuma cifra encontrada.</div>

    <ul v-else class="fb-list">
      <li v-for="cipher in items" :key="cipher.id" class="fb-row" :class="{ busy: busyId === cipher.id }">
        <div class="fb-main">
          <h3 class="fb-title">{{ cipher.name }}</h3>
          <p class="fb-text">{{ cipher.description }}</p>
          <div class="fb-meta">
            <span class="fb-type" :class="`fb-type-${cipher.difficulty}`">{{ cipher.difficulty }}</span>
          </div>
        </div>
        <div class="fb-actions">
          <button class="btn btn-danger" type="button" :disabled="busyId === cipher.id" @click="remove(cipher)">
            Excluir
          </button>
        </div>
      </li>
    </ul>
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

.fb-title {
  margin: 0;
  font-size: 15px;
  font-weight: 600;
  color: var(--text-primary);
}

.fb-text {
  margin: 6px 0;
  font-size: 14px;
  color: var(--text-secondary);
}

.fb-meta {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.fb-type {
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
}

.fb-type-easy {
  color: var(--accent-green);
  background: var(--accent-green-muted);
}

.fb-type-medium {
  color: var(--accent-yellow);
  background: var(--accent-yellow-muted);
}

.fb-type-hard {
  color: var(--accent-red);
  background: var(--accent-red-muted);
}

.fb-actions {
  display: flex;
  align-items: center;
  gap: 8px;
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

.btn-danger {
  color: var(--accent-red);
  background: transparent;
  border-color: var(--border);
}

.btn-danger:hover:not(:disabled) {
  background: var(--accent-red-muted);
  border-color: var(--accent-red);
}

@media (max-width: 640px) {
  .fb-row {
    flex-direction: column;
  }
  .fb-actions {
    width: 100%;
    justify-content: flex-end;
  }
}
</style>