<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'
import { getAdminMetrics } from '../services/serviceAdminMetrics'
import type { SystemMetrics } from '../types/typeAdminMetrics'

const emit = defineEmits<{ forbidden: [] }>()

const metrics = ref<SystemMetrics | null>(null)
const loading = ref(true)
const error = ref(false)

const numberFormat = new Intl.NumberFormat('pt-BR')

const cards = computed<{ label: string; value: number; tint: 'accent' | 'green' | 'yellow' }[]>(() => {
  const acessos = metrics.value?.acessos ?? { total: 0, mes: 0, semana: 0 }
  const ativos = metrics.value?.usuarios_ativos ?? { total: 0, mes: 0, semana: 0 }
  return [
    { label: 'Acessos (total)', value: acessos.total, tint: 'accent' },
    { label: 'Acessos (30 dias)', value: acessos.mes, tint: 'accent' },
    { label: 'Acessos (7 dias)', value: acessos.semana, tint: 'accent' },
    { label: 'Usuários ativos (total)', value: ativos.total, tint: 'green' },
    { label: 'Usuários ativos (30 dias)', value: ativos.mes, tint: 'green' },
    { label: 'Usuários ativos (7 dias)', value: ativos.semana, tint: 'yellow' },
  ]
})

function isForbidden(e: unknown): boolean {
  return axios.isAxiosError(e) && e.response?.status === 403
}

async function load() {
  loading.value = true
  error.value = false
  try {
    metrics.value = await getAdminMetrics()
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

onMounted(() => load())
</script>

<template>
  <section class="admin-section">
    <header class="section-header">
      <div>
        <h2 class="section-title">M&eacute;tricas do sistema</h2>
        <p class="section-sub">Acessos e usu&aacute;rios ativos (contas de administrador n&atilde;o contam).</p>
      </div>
      <button class="btn" type="button" :disabled="loading" @click="load">Atualizar</button>
    </header>

    <div v-if="loading" class="state-box">Calculando m&eacute;tricas...</div>

    <div v-else-if="error" class="state-box">N&atilde;o foi poss&iacute;vel carregar as m&eacute;tricas.</div>

    <div v-else class="metric-grid">
      <div v-for="card in cards" :key="card.label" class="metric-card">
        <span class="metric-dot" :class="`metric-dot-${card.tint}`" aria-hidden="true"></span>
        <span class="metric-label">{{ card.label }}</span>
        <strong class="metric-value">{{ numberFormat.format(card.value) }}</strong>
      </div>
    </div>
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

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 14px;
  font-size: 13px;
  font-weight: 500;
  border-radius: var(--radius-md);
  border: 1px solid var(--border);
  cursor: pointer;
  font-family: var(--font-body);
  color: var(--text-primary);
  background: var(--bg-overlay);
  transition: border-color 0.15s ease, background 0.15s ease;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn:hover:not(:disabled) {
  border-color: var(--border-emphasis);
}

.metric-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 12px;
}

.metric-card {
  display: flex;
  flex-direction: column;
  gap: 6px;
  padding: 16px;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  background: var(--bg-inset);
}

.metric-dot {
  width: 8px;
  height: 8px;
  border-radius: var(--radius-full);
}

.metric-dot-accent {
  background: var(--accent);
}

.metric-dot-green {
  background: var(--accent-green);
}

.metric-dot-yellow {
  background: var(--accent-yellow);
}

.metric-label {
  font-size: 12px;
  color: var(--text-secondary);
}

.metric-value {
  font-size: 26px;
  font-weight: 700;
  font-variant-numeric: tabular-nums;
  color: var(--text-primary);
  font-family: var(--font-mono);
}
</style>