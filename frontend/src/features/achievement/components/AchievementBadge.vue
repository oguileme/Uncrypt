<script setup lang="ts">
import { computed } from 'vue'
import type { Achievement, AchievementProgressUser } from '../types/typeAchievement'
import AchievementIcon from './AchievementIcon.vue'

const props = defineProps<{
  achievement: Achievement
  progress: AchievementProgressUser | undefined
}>()

const isCompleted = computed(() => props.progress?.is_completed ?? false)
const progressValue = computed(() => props.progress?.progress ?? 0)
const percent = computed(() =>
  Math.min(100, Math.round((progressValue.value / props.achievement.required_count) * 100)),
)
const inProgress = computed(() => !isCompleted.value && progressValue.value > 0)
const locked = computed(() => !isCompleted.value && progressValue.value === 0)

const formattedCount = computed(() => {
  const req = props.achievement.required_count
  return props.achievement.icon === 'star' || props.achievement.icon === 'compass'
    ? `${progressValue.value}/${req} XP`
    : `${progressValue.value}/${req}`
})
</script>

<template>
  <article
    class="badge-card"
    :class="[
      `badge-${achievement.color}`,
      { completed: isCompleted, 'in-progress': inProgress, locked },
    ]"
  >
    <div class="badge-ico" :class="`ico-${achievement.color}`">
      <template v-if="isCompleted">
        <AchievementIcon v-if="achievement.icon === 'star'" :kind="achievement.icon" />
        <AchievementIcon v-else :kind="achievement.icon" />
      </template>
      <AchievementIcon v-else :kind="achievement.icon" />
    </div>

    <div class="badge-body">
      <h3 class="badge-name">{{ achievement.name }}</h3>
      <p class="badge-desc">{{ achievement.description }}</p>

      <div class="badge-foot">
        <div v-if="isCompleted" class="badge-state state-complete">
          <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor">
            <path
              d="M13.78 4.22a.75.75 0 0 1 0 1.06l-7.25 7.25a.75.75 0 0 1-1.06 0L2.22 9.28a.749.749 0 0 1 1.06-1.06L6 10.94l6.72-6.72a.75.75 0 0 1 1.06 0Z"
            />
          </svg>
          Desbloqueada
        </div>

        <template v-else>
          <div class="badge-progress">
            <div class="progress-track">
              <div class="progress-fill" :style="{ width: percent + '%' }"></div>
            </div>
            <span v-if="inProgress" class="progress-count">{{ formattedCount }}</span>
            <span v-else class="progress-count">0/{{ achievement.required_count }}</span>
          </div>
          <div v-if="locked" class="badge-lock">
            <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor">
              <rect x="3" y="7.5" width="10" height="7" rx="1.5" />
              <path d="M5.5 7.5V5.5a2.5 2.5 0 0 1 5 0v2" />
            </svg>
            Bloqueada
          </div>
        </template>
      </div>
    </div>

    <span class="badge-xp"><strong>+{{ achievement.xp_reward }}</strong> XP</span>
  </article>
</template>

<style scoped>
.badge-card {
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 14px;
  padding: 20px;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

.locked {
  opacity: 0.62;
}

.completed {
  border-color: rgba(63, 185, 80, 0.45);
  box-shadow: 0 0 0 1px rgba(63, 185, 80, 0.12), 0 0 18px -6px rgba(63, 185, 80, 0.35);
}

.in-progress {
  border-color: var(--border-emphasis);
}

.badge-card:hover {
  transform: translateY(-2px);
  border-color: var(--border-emphasis);
}

/* Badge octogonal */
.badge-ico {
  width: 56px;
  height: 56px;
  display: flex;
  align-items: center;
  justify-content: center;
  clip-path: polygon(50% 0%, 93% 13%, 100% 50%, 93% 87%, 50% 100%, 7% 87%, 0% 50%, 7% 13%);
}

.ico-green {
  background: var(--accent-green-muted);
  color: var(--accent-green);
}

.ico-blue {
  background: var(--accent-blue-muted);
  color: var(--accent-blue);
}

.ico-yellow {
  background: var(--accent-yellow-muted);
  color: var(--accent-yellow);
}

.ico-orange {
  background: var(--accent-yellow-muted);
  color: var(--accent-orange);
}

.ico-purple {
  background: var(--accent-purple-muted);
  color: var(--accent-purple);
}

.badge-card.completed .badge-ico {
  animation: pulse-glow 3.5s ease-in-out infinite;
}

.badge-body {
  display: flex;
  flex-direction: column;
  gap: 6px;
  min-height: 76px;
}

.badge-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.badge-desc {
  font-size: 12.5px;
  color: var(--text-secondary);
  line-height: 1.5;
  flex: 1;
}

.badge-foot {
  margin-top: 4px;
}

.badge-state {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11.5px;
  font-weight: 600;
  font-family: var(--font-mono);
  letter-spacing: 0.02em;
}

.state-complete {
  color: var(--accent-green);
}

.badge-progress {
  display: flex;
  align-items: center;
  gap: 10px;
}

.progress-track {
  flex: 1;
  height: 6px;
  background: var(--bg-emphasis);
  border-radius: var(--radius-full);
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, var(--accent-green), var(--accent-blue));
  border-radius: var(--radius-full);
  transition: width 0.6s var(--ease-out);
}

.progress-count {
  font-size: 11.5px;
  font-family: var(--font-mono);
  color: var(--text-subtle);
  white-space: nowrap;
}

.badge-lock {
  display: flex;
  align-items: center;
  gap: 5px;
  margin-top: 7px;
  font-size: 11.5px;
  font-weight: 600;
  font-family: var(--font-mono);
  color: var(--text-subtle);
}

.badge-xp {
  align-self: flex-start;
  font-size: 12px;
  color: var(--text-secondary);
  font-family: var(--font-mono);
}

.badge-xp strong {
  color: var(--accent-yellow);
}
</style>
