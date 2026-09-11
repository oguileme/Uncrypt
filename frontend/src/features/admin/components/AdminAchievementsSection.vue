<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { getAchievements, deleteAchievement } from '@/features/achievement/services/serviceAchievement'
import type { Achievement } from '@/features/achievement/types/typeAchievement'

const emit = defineEmits<{ forbidden: [] }>()

const items = ref<Achievement[]>([])
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
    items.value = await getAchievements()
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

async function remove(achievement: Achievement) {
  const confirmed = window.confirm(`Excluir conquista "${achievement.name}"?`)
  if (!confirmed) return
  busyId.value = achievement.id
  try {
    await deleteAchievement(achievement.id)
    items.value = items.value.filter((a) => a.id !== achievement.id)
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
        <h2 class="section-title">Conquistas</h2>
        <p class="section-sub">{{ items.length }} registro{{ items.length === 1 ? '' : 's' }}</p>
      </div>
    </header>

    <div v-if="loading" class="state-box">Carregando conquistas...</div>

    <div v-else-if="error" class="state-box">N&atilde;o foi poss&iacute;vel carregar as conquistas.</div>

    <div v-else-if="items.length === 0" class="state-box">Nenhuma conquista encontrada.</div>

    <ul v-else class="fb-list">
      <li v-for="achievement in items" :key="achievement.id" class="fb-row" :class="{ busy: busyId === achievement.id }">
        <div class="fb-main">
          <h3 class="fb-title">{{ achievement.name }}</h3>
          <p class="fb-text">{{ achievement.description }}</p>
          <div class="fb-meta">
            <span class="fb-type" :class="`fb-type-${achievement.color}`">{{ achievement.color }}</span>
            <span class="fb-date">{{ achievement.icon }}</span>
            <span class="fb-date">XP {{ achievement.xp_reward }}</span>
            <span class="fb-date">{{ achievement.required_count }} requisitos</span>
          </div>
        </div>
        <div class="fb-actions">
          <button class="btn btn-danger" type="button" :disabled="busyId === achievement.id" @click="remove(achievement)">
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

.fb-type-green {
  color: var(--accent-green);
  background: var(--accent-green-muted);
}

.fb-type-blue {
  color: var(--accent-blue);
  background: var(--accent-blue-muted);
}

.fb-type-yellow {
  color: var(--accent-yellow);
  background: var(--accent-yellow-muted);
}

.fb-type-purple {
  color: var(--accent-purple);
  background: var(--accent-purple-muted);
}

.fb-type-orange {
  color: var(--accent-orange);
  background: var(--accent-orange-muted);
}

.fb-date {
  font-size: 12px;
  color: var(--text-subtle);
  font-family: var(--font-mono);
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