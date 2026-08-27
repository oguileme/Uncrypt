<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getRecentActivity, type RecentActivityType } from '../services/userService'

const activities = ref<RecentActivityType[]>([])
const loading = ref(true)

onMounted(async () => {
  try {
    activities.value = await getRecentActivity()
  } catch {
    activities.value = []
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="activity">
    <div class="section-header">
      <h2 class="section-title">Atividade Recente</h2>
    </div>

    <div v-if="loading" class="activity-empty">Carregando...</div>

    <div v-else-if="activities.length === 0" class="activity-empty">
      Nenhuma atividade ainda. Complete um desafio para aparecer aqui.
    </div>

    <div v-else class="activity-list">
      <div v-for="(a, index) in activities" :key="a.id" class="activity-item">
        <div class="activity-line">
          <div
            class="activity-dot"
            :class="a.result === 'correct' ? 'dot-correct' : 'dot-wrong'"
          ></div>
          <div v-if="index < activities.length - 1" class="activity-connector"></div>
        </div>

        <div class="activity-content">
          <div class="activity-top">
            <span class="activity-challenge">{{ a.challenge }}</span>
            <span
              :class="['activity-result', a.result === 'correct' ? 'result-correct' : 'result-wrong']"
            >
              <svg
                v-if="a.result === 'correct'"
                width="12"
                height="12"
                viewBox="0 0 16 16"
                fill="currentColor"
              >
                <path d="M13.78 4.22a.75.75 0 0 1 0 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0L2.22 9.28a.75.75 0 0 1 1.06-1.06L6 10.94l6.72-6.72a.75.75 0 0 1 1.06 0Z" />
              </svg>
              <svg
                v-else
                width="12"
                height="12"
                viewBox="0 0 16 16"
                fill="currentColor"
              >
                <path d="M3.72 3.72a.75.75 0 0 1 1.06 0L8 6.94l3.22-3.22a.749.749 0 0 1 1.275.326.749.749 0 0 1-.215.734L9.06 8l3.22 3.22a.749.749 0 0 1-.326 1.275.749.749 0 0 1-.734-.215L8 9.06l-3.22 3.22a.751.751 0 0 1-1.042-.018.751.751 0 0 1-.018-1.042L6.94 8 3.72 4.78a.75.75 0 0 1 0-1.06Z" />
              </svg>
            </span>
          </div>
          <span class="activity-time">{{ a.time }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.activity {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}

.section-header {
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-muted);
}

.section-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.activity-list {
  padding: 8px 0;
}

.activity-empty {
  padding: 24px 20px;
  font-size: 13px;
  color: var(--text-subtle);
  text-align: center;
}

.activity-item {
  display: flex;
  gap: 16px;
  padding: 0 20px;
}

.activity-line {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 16px;
  flex-shrink: 0;
  padding-top: 18px;
}

.activity-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  flex-shrink: 0;
}

.dot-correct {
  background: var(--accent-green);
}

.dot-wrong {
  background: var(--accent-red);
}

.activity-connector {
  width: 2px;
  flex: 1;
  background: var(--border-muted);
  margin-top: 4px;
}

.activity-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 4px;
  padding: 14px 0;
  border-bottom: 1px solid var(--border-muted);
}

.activity-item:last-child .activity-content {
  border-bottom: none;
}

.activity-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.activity-challenge {
  font-size: 13px;
  color: var(--text-primary);
  font-weight: 500;
}

.activity-result {
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

.result-correct {
  color: var(--accent-green);
}

.result-wrong {
  color: var(--accent-red);
}

.activity-time {
  font-size: 12px;
  color: var(--text-subtle);
}
</style>
