<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  level?: number
  xpProgress?: number
  xpLevelup?: number
  accuracyRate?: number
  challengesCompleted?: number
  avgTime?: number
}

const props = defineProps<Props>()

const rankLabel = computed(() => {
  const l = props.level
  if (l == null) return null
  if (l <= 2) return 'Iniciante'
  if (l <= 4) return 'Cript\u00f3grafo J\u00fanior'
  if (l <= 6) return 'Cript\u00f3grafo Pleno'
  return 'Cript\u00f3grafo S\u00eanior'
})

const xpPercent = computed(() => {
  const { xpProgress, xpLevelup } = props
  if (xpProgress == null || !xpLevelup) return 0
  return Math.min(100, Math.round((xpProgress / xpLevelup) * 100))
})

const xpRemaining = computed(() => {
  const { xpProgress, xpLevelup } = props
  if (xpProgress == null || xpLevelup == null) return null
  return Math.max(0, xpLevelup - xpProgress)
})

function formatAvgTime(seconds?: number): string {
  if (seconds == null) return '\u2014'
  const s = Math.round(seconds)
  if (s < 60) return `${s}s`
  const m = Math.floor(s / 60)
  const rest = s % 60
  return rest ? `${m}m ${rest}s` : `${m}m`
}
</script>

<template>
  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon icon-green">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M12 2L2 7l10 5 10-5-10-5z" />
          <path d="M2 17l10 5 10-5" />
          <path d="M2 12l10 5 10-5" />
        </svg>
      </div>
      <div class="stat-info">
        <span class="stat-value">{{ level ?? '\u2014' }}</span>
        <span class="stat-label">N&iacute;vel</span>
      </div>
      <span v-if="rankLabel" class="stat-sub">{{ rankLabel }}</span>
    </div>

    <div class="stat-card">
      <div class="stat-icon icon-blue">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 16.8l-6.2 4.5 2.4-7.4L2 9.4h7.6z" />
        </svg>
      </div>
      <div class="stat-info">
        <span class="stat-value">{{ xpProgress ?? '\u2014' }}</span>
        <span class="stat-label">XP do N&iacute;vel</span>
      </div>
      <div v-if="xpProgress != null && xpLevelup != null" class="stat-progress">
        <div class="progress-bar">
          <div class="progress-fill" :style="{ width: xpPercent + '%' }"></div>
        </div>
        <span class="progress-text">
          {{ xpRemaining === 0 ? 'Nível completo!' : `Faltam ${xpRemaining} XP` }}
        </span>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon icon-orange">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <circle cx="12" cy="13" r="8" />
          <path d="M12 9v4l2.5 2.5" />
          <path d="M9 2h6" />
        </svg>
      </div>
      <div class="stat-info">
        <span class="stat-value">{{ formatAvgTime(avgTime) }}</span>
        <span class="stat-label">Tempo M&eacute;dio</span>
      </div>
      <span class="stat-sub">por desafio resolvido</span>
    </div>

    <div class="stat-card">
      <div class="stat-icon icon-purple">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
          <polyline points="22 4 12 14.01 9 11.01" />
        </svg>
      </div>
      <div class="stat-info">
        <span class="stat-value">{{ accuracyRate != null ? accuracyRate + '%' : '\u2014' }}</span>
        <span class="stat-label">Taxa de Acerto</span>
      </div>
      <span v-if="challengesCompleted != null" class="stat-sub">
        {{ challengesCompleted }} desafio{{ challengesCompleted === 1 ? '' : 's' }} resolvido{{
          challengesCompleted === 1 ? '' : 's'
        }}
      </span>
    </div>
  </div>
</template>

<style scoped>
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.stat-card {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  transition: border-color 0.15s ease, transform 0.15s ease;
}

.stat-card:hover {
  border-color: var(--border-emphasis);
  transform: translateY(-1px);
}

.stat-icon {
  width: 36px;
  height: 36px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
}

.icon-green {
  background: var(--accent-green-muted);
  color: var(--accent-green);
}

.icon-blue {
  background: var(--accent-blue-muted);
  color: var(--accent-blue);
}

.icon-orange {
  background: var(--accent-yellow-muted);
  color: var(--accent-orange);
}

.icon-purple {
  background: var(--accent-purple-muted);
  color: var(--accent-purple);
}

.stat-info {
  display: flex;
  align-items: baseline;
  gap: 8px;
}

.stat-value {
  font-size: 28px;
  font-weight: 700;
  color: var(--text-primary);
  font-family: var(--font-mono);
  line-height: 1;
}

.stat-label {
  font-size: 13px;
  color: var(--text-secondary);
  font-weight: 500;
}

.stat-sub {
  font-size: 12px;
  color: var(--text-subtle);
}

.stat-progress {
  display: flex;
  flex-direction: column;
  gap: 6px;
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

.progress-text {
  font-size: 11px;
  color: var(--text-subtle);
}

@media (max-width: 1024px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 480px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}
</style>
