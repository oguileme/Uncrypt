<script setup lang="ts">
export type AchievementFilterKind = 'all' | 'completed' | 'progress'

defineProps<{
  modelValue: AchievementFilterKind
  counts: { all: number; completed: number; progress: number }
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: AchievementFilterKind): void
}>()

const options: { key: AchievementFilterKind; label: string }[] = [
  { key: 'all', label: 'Todos' },
  { key: 'completed', label: 'Desbloqueadas' },
  { key: 'progress', label: 'Em andamento' },
]
</script>

<template>
  <div class="filter-bar" role="tablist" aria-label="Filtrar conquistas">
    <button
      v-for="opt in options"
      :key="opt.key"
      role="tab"
      :aria-selected="modelValue === opt.key"
      class="filter-tab"
      :class="{ active: modelValue === opt.key }"
      @click="emit('update:modelValue', opt.key)"
    >
      {{ opt.label }}
      <span class="filter-count">{{ counts[opt.key] }}</span>
    </button>
  </div>
</template>

<style scoped>
.filter-bar {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.filter-tab {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 7px 14px;
  font-size: 13px;
  font-weight: 500;
  font-family: var(--font-body);
  color: var(--text-secondary);
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-full);
  cursor: pointer;
  transition: color 0.15s ease, border-color 0.15s ease, background 0.15s ease;
}

.filter-tab:hover {
  color: var(--text-primary);
  border-color: var(--border-emphasis);
}

.filter-tab.active {
  color: var(--accent-green);
  background: var(--accent-green-muted);
  border-color: rgba(35, 134, 54, 0.35);
}

.filter-count {
  font-size: 11px;
  font-family: var(--font-mono);
  padding: 0 7px;
  line-height: 1.4;
  border-radius: var(--radius-full);
  background: var(--bg-emphasis);
  color: var(--text-subtle);
}

.filter-tab.active .filter-count {
  background: var(--bg-surface);
  color: var(--accent-green);
}
</style>
