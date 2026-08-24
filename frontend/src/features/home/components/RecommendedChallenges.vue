<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getRecommendedChallenges } from '@/features/challenge/services/servicesChallenge'
import { createChallengeUser } from '@/features/challenge/services/serviceChallengeUser'
import type { ChallengeType } from '@/features/challenge/types/typeChallenge'
import { getTypeColor, difficultyToStars } from '@/features/challenge/utils/cipherStyles'

const router = useRouter()

const MAX_SHOWN = 3

const recommendations = ref<ChallengeType[]>([])
const loading = ref(true)
const startingId = ref<number | null>(null)
const error = ref<string | null>(null)

const visible = computed(() => recommendations.value.slice(0, MAX_SHOWN))
const hiddenCount = computed(() => Math.max(0, recommendations.value.length - MAX_SHOWN))

onMounted(async () => {
  try {
    recommendations.value = await getRecommendedChallenges()
  } catch {
    error.value = 'Nao foi possivel carregar as recomendacoes.'
  } finally {
    loading.value = false
  }
})

async function start(c: ChallengeType) {
  if (startingId.value !== null) return
  startingId.value = c.id
  try {
    const record = await createChallengeUser({ challenge_id: c.id })
    router.push({ name: 'challenge-detail', params: { id: String(record.id) } })
  } catch {
    error.value = 'Nao foi possivel iniciar o desafio.'
    startingId.value = null
  }
}

function getBadgeClass(name?: string) {
  return `type-badge type-${getTypeColor(name ?? '')}`
}

function getStars(c: ChallengeType) {
  return Array.from(
    { length: 5 },
    (_, i) => i < difficultyToStars(c.type_encryption?.difficulty ?? 'easy'),
  )
}
</script>

<template>
  <div class="recommended">
    <div class="section-header">
      <h2 class="section-title">Desafios Recomendados</h2>
      <span v-if="!loading && !error" class="section-badge">{{ recommendations.length }}</span>
    </div>

    <div v-if="loading" class="state-box">Carregando recomendacoes...</div>

    <div v-else-if="error" class="feedback-wrong state-box">{{ error }}</div>

    <div v-else-if="recommendations.length === 0" class="state-box">
      Voce concluiu todos os desafios disponiveis. Aguarde novas cifras!
    </div>

    <div v-else class="challenge-list">
      <button
        v-for="c in visible"
        :key="c.id"
        class="recommended-item"
        :disabled="startingId !== null"
        @click="start(c)"
      >
        <div class="recommended-top">
          <div class="recommended-badges">
            <span :class="getBadgeClass(c.type_encryption?.name)">
              {{ c.type_encryption?.name }}
            </span>
            <span class="recommended-xp">{{ c.xp }} XP</span>
          </div>
          <div class="recommended-stars">
            <svg
              v-for="(filled, i) in getStars(c)"
              :key="i"
              width="12"
              height="12"
              viewBox="0 0 16 16"
              :fill="filled ? '#d29922' : 'none'"
              :stroke="filled ? '#d29922' : '#6e7681'"
              stroke-width="1.5"
            >
              <path d="M8 1.5l2 4 4.5.7-3.2 3.1.8 4.4L8 11.3l-4.1 2.4.8-4.4L1.5 6.2l4.5-.7z" />
            </svg>
          </div>
        </div>

        <h3 class="recommended-title">{{ c.title }}</h3>
        <p class="recommended-desc">{{ c.description }}</p>

        <div class="recommended-bottom">
          <span class="btn-start">
            {{ startingId === c.id ? 'Iniciando...' : 'Iniciar' }}
          </span>
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M6 4l4 4-4 4" />
          </svg>
        </div>
      </button>

      <RouterLink v-if="hiddenCount > 0" to="/challenge" class="see-all">
        Ver todos os {{ recommendations.length }} desafios
        <svg width="12" height="12" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
          <path d="M6 4l4 4-4 4" />
        </svg>
      </RouterLink>
    </div>
  </div>
</template>

<style scoped>
.recommended {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-muted);
}

.section-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.section-badge {
  font-size: 12px;
  color: var(--text-subtle);
  background: var(--bg-emphasis);
  padding: 2px 8px;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
}

.state-box {
  padding: 32px 20px;
  text-align: center;
  font-size: 13px;
  color: var(--text-secondary);
}

.challenge-list {
  display: flex;
  flex-direction: column;
}

.recommended-item {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-muted);
  background: transparent;
  border-left: none;
  border-right: none;
  border-top: none;
  width: 100%;
  text-align: left;
  font-family: var(--font-body);
  cursor: pointer;
  transition: background 0.15s ease;
}

.recommended-item:last-of-type {
  border-bottom: none;
}

.recommended-item:hover:not(:disabled) {
  background: var(--bg-emphasis);
}

.recommended-item:disabled {
  opacity: 0.6;
  cursor: wait;
}

.recommended-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.recommended-badges {
  display: flex;
  align-items: center;
  gap: 8px;
}

.type-badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  font-size: 11px;
  font-weight: 600;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
  letter-spacing: 0.02em;
}

.type-green {
  background: var(--accent-green-muted);
  color: var(--accent-green);
}

.type-yellow {
  background: var(--accent-yellow-muted);
  color: var(--accent-yellow);
}

.type-purple {
  background: var(--accent-purple-muted);
  color: var(--accent-purple);
}

.recommended-xp {
  font-size: 11px;
  font-weight: 600;
  color: var(--accent-yellow);
  font-family: var(--font-mono);
}

.recommended-stars {
  display: flex;
  gap: 1px;
}

.recommended-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.recommended-desc {
  font-size: 13px;
  color: var(--text-secondary);
  line-height: 1.4;
}

.recommended-bottom {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-top: 4px;
}

.btn-start {
  font-size: 12px;
  font-weight: 500;
  color: var(--accent-blue);
  transition: color 0.15s ease;
}

.recommended-item:hover:not(:disabled) .btn-start {
  color: #58a6ff;
}

.recommended-item:hover:not(:disabled) svg {
  transform: translateX(2px);
  transition: transform 0.15s ease;
}

.see-all {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 12px 20px;
  border-top: 1px solid var(--border-muted);
  font-size: 12px;
  font-weight: 500;
  color: var(--text-secondary);
  text-decoration: none;
  transition: background 0.15s ease, color 0.15s ease;
}

.see-all:hover {
  background: var(--bg-emphasis);
  color: var(--accent-blue);
}
</style>
