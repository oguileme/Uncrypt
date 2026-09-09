<script setup lang="ts">
import { computed } from 'vue'
import { formatTime } from '../utils/formatTime'

const props = defineProps<{
  elapsed: number
}>()

const display = computed(() => formatTime(props.elapsed))
</script>

<template>
  <span class="timer-pill" role="timer" aria-label="Tempo decorrido">
    <svg
      width="12"
      height="12"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="2"
      stroke-linecap="round"
      stroke-linejoin="round"
      aria-hidden="true"
    >
      <circle cx="12" cy="13" r="8" />
      <path d="M12 9v4l2.5 2.5" />
      <path d="M9 2h6" />
    </svg>
    <span class="timer-value">{{ display }}</span>
  </span>
</template>

<style scoped>
.timer-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  font-family: var(--font-mono);
  font-size: 12px;
  color: var(--text-secondary);
  background: var(--bg-emphasis);
  border: 1px solid var(--border);
  border-radius: var(--radius-full);
  white-space: nowrap;
}

.timer-value {
  font-variant-numeric: tabular-nums;
  color: var(--text-primary);
}
</style>