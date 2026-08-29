<script setup lang="ts">
import { computed } from 'vue'
import type { Achievement, AchievementProgressUser } from '../types/typeAchievement'

const props = defineProps<{
  achievements: Achievement[]
  progress: AchievementProgressUser[]
}>()

const completedCount = computed(
  () => props.achievements.filter((a) => props.progress.find((p) => p.achievement_id === a.id)?.is_completed).length,
)
const unlockedXp = computed(() =>
  props.achievements
    .filter((a) => props.progress.find((p) => p.achievement_id === a.id)?.is_completed)
    .reduce((sum, a) => sum + a.xp_reward, 0),
)
const percent = computed(() =>
  Math.round((completedCount.value / props.achievements.length) * 100),
)

const kitLabel = computed(() => {
  const p = percent.value
  if (p === 0) return 'Arsenal Vazio'
  if (p < 34) return 'Kit do Iniciante'
  if (p < 67) return 'Kit do Criptógrafo'
  if (p < 100) return 'Kit do Criptógrafo Pleno'
  return 'Arsenal Completo'
})
</script>

<template>
  <div class="arsenal-header">
    <div class="arsenal-emblema">
      <span class="emblema-pct">{{ percent }}<small>%</small></span>
      <span class="emblema-label">dominado</span>
    </div>

    <div class="arsenal-info">
      <div class="arsenal-gauge">
        <div class="gauge-track">
          <div class="gauge-fill" :style="{ width: percent + '%' }"></div>
        </div>
      </div>

      <div class="arsenal-stats">
        <div class="stat-chip">
          <span class="chip-value">{{ completedCount }}/{{ achievements.length }}</span>
          <span class="chip-label">peças desbloqueadas</span>
        </div>
        <div class="stat-chip">
          <span class="chip-value chip-xp">+{{ unlockedXp }}</span>
          <span class="chip-label">XP de recompensa</span>
        </div>
      </div>
    </div>

    <span class="arsenal-kit">{{ kitLabel }}</span>
  </div>
</template>

<style scoped>
.arsenal-header {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 28px;
  padding: 24px;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
}

.arsenal-emblema {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 2px;
  padding-right: 28px;
  border-right: 1px solid var(--border-muted);
}

.emblema-pct {
  font-family: var(--font-mono);
  font-size: 36px;
  font-weight: 700;
  line-height: 1;
  background: linear-gradient(135deg, var(--accent-green), var(--accent-blue));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.emblema-pct small {
  font-size: 18px;
  -webkit-text-fill-color: var(--text-secondary);
  color: var(--text-secondary);
}

.emblema-label {
  font-size: 11.5px;
  color: var(--text-subtle);
  font-family: var(--font-mono);
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

.arsenal-info {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.arsenal-gauge {
  width: 100%;
}

.gauge-track {
  height: 10px;
  background: var(--bg-emphasis);
  border-radius: var(--radius-full);
  overflow: hidden;
}

.gauge-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--accent-green), var(--accent-blue), var(--accent-purple));
  border-radius: var(--radius-full);
  transition: width 0.8s var(--ease-out);
}

.arsenal-stats {
  display: flex;
  gap: 32px;
}

.stat-chip {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.chip-value {
  font-size: 18px;
  font-weight: 700;
  font-family: var(--font-mono);
  color: var(--text-primary);
}

.chip-xp {
  color: var(--accent-yellow);
}

.chip-label {
  font-size: 11.5px;
  color: var(--text-subtle);
}

.arsenal-kit {
  padding: 6px 14px;
  font-size: 12.5px;
  font-weight: 600;
  font-family: var(--font-mono);
  letter-spacing: 0.02em;
  background: var(--accent-green-muted);
  color: var(--accent-green);
  border: 1px solid rgba(35, 134, 54, 0.3);
  border-radius: var(--radius-full);
  white-space: nowrap;
}

@media (max-width: 720px) {
  .arsenal-header {
    grid-template-columns: 1fr;
    gap: 20px;
  }

  .arsenal-emblema {
    flex-direction: row;
    align-items: baseline;
    gap: 10px;
    padding-right: 0;
    padding-bottom: 16px;
    border-right: none;
    border-bottom: 1px solid var(--border-muted);
  }

  .arsenal-kit {
    align-self: flex-start;
  }
}
</style>
