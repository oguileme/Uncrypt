<script setup lang="ts">
import { ref, computed } from 'vue'
import { mockAchievements, mockProgress } from '../data/mockAchievements'
import type { Achievement } from '../types/typeAchievement'
import AchievementBadge from '../components/AchievementBadge.vue'
import AchievementArsenalHeader from '../components/AchievementArsenalHeader.vue'
import AchievementFilter, { type AchievementFilterKind } from '../components/AchievementFilter.vue'

const achievements = ref<Achievement[]>(mockAchievements)
const progress = mockProgress
const filter = ref<AchievementFilterKind>('all')

function progressFor(id: number) {
  return progress.find((p) => p.achievement_id === id)
}

const counts = computed(() => ({
  all: achievements.value.length,
  completed: achievements.value.filter((a) => progressFor(a.id)?.is_completed).length,
  progress: achievements.value.filter((a) => {
    const p = progressFor(a.id)
    return p && !p.is_completed && p.progress > 0
  }).length,
}))

const visible = computed(() => {
  return achievements.value
    .map((a) => ({ a, p: progressFor(a.id) }))
    .filter(({ p }) => {
      if (filter.value === 'completed') return p?.is_completed
      if (filter.value === 'progress') return p && !p.is_completed && p.progress > 0
      return true
    })
    .sort((x, y) => {
      const comp = (z: { p: ReturnType<typeof progressFor> }) =>
        z.p?.is_completed ? 2 : z.p && z.p.progress > 0 ? 1 : 0
      return comp(y) - comp(x)
    })
})
</script>

<template>
  <main class="achievements-page">
    <div class="page-inner">
      <header class="page-header">
        <h1 class="page-title">
          Conquistas
          <span class="page-title-hint">~ arsenal</span>
        </h1>
        <p class="page-sub">
          Desbloqueie ferramentas e fortaleça seu arsenal criptográfico, conquista a conquista.
        </p>
      </header>

      <AchievementArsenalHeader :achievements="achievements" :progress="progress" />

      <AchievementFilter v-model="filter" :counts="counts" />

      <section class="arsenal-grid" aria-label="Lista de conquistas">
        <AchievementBadge
          v-for="{ a, p } in visible"
          :key="a.id"
          :achievement="a"
          :progress="p"
        />
      </section>

      <section class="empty-warning" v-if="visible.length === 0">
        Nenhuma conquista nesta aba ainda. Continue decifrando!
      </section>
    </div>
  </main>
</template>

<style scoped>
.achievements-page {
  flex: 1;
  padding: 32px 24px;
}

.page-inner {
  max-width: 1080px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 20px;
  animation: fadeIn 0.4s var(--ease-out);
}

.page-header {
  margin-bottom: 4px;
}

.page-title {
  font-size: 24px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 4px;
}

.page-title-hint {
  font-size: 13px;
  font-weight: 500;
  font-family: var(--font-mono);
  color: var(--text-subtle);
  margin-left: 8px;
}

.page-sub {
  font-size: 14px;
  color: var(--text-secondary);
}

.arsenal-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 16px;
}

.empty-warning {
  padding: 24px;
  text-align: center;
  font-size: 13px;
  color: var(--text-subtle);
}

@media (max-width: 768px) {
  .achievements-page {
    padding: 20px 16px;
  }

  .page-title {
    font-size: 20px;
  }
}
</style>
