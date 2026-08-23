<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { getChallenges } from '../services/servicesChallenge'
import { getTypeEncryptions } from '../services/serviceTypeEncryption'
import { createChallengeUser, getChallengeUsers } from '../services/serviceChallengeUser'
import type { TypeEncryptionType } from '../types/typeTypeEncryption'
import type { ChallengeType } from '../types/typeChallenge'
import type { ChallengeUserType } from '../types/typeChallangeUser'
import { useAuth } from '@/features/auth/composables/useAuth'
import { getTypeColor, difficultyToStars } from '../utils/cipherStyles'

const router = useRouter()
const { user } = useAuth()

const types = ref<TypeEncryptionType[]>([])
const challenges = ref<ChallengeType[]>([])
const myRecords = ref<ChallengeUserType[]>([])
const loading = ref(true)
const startingId = ref<number | null>(null)
const error = ref<string | null>(null)

const grouped = computed(() => {
  return types.value.map((t) => ({
    ...t,
    color: getTypeColor(t.name),
    stars: difficultyToStars(t.difficulty),
    challenges: challenges.value.filter((c) => c.type_encryption_id === t.id),
  }))
})

function recordFor(challengeId: number): ChallengeUserType | undefined {
  return myRecords.value.find(
    (r) => r.challenge_id === challengeId && r.user_id === user.value?.id,
  )
}

async function loadData() {
  // carregamento independente: se uma chamada falhar, o que vier e renderizado
  const [typesRes, challengesRes, recordsRes] = await Promise.allSettled([
    getTypeEncryptions(),
    getChallenges(),
    getChallengeUsers(),
  ])

  types.value = typesRes.status === 'fulfilled' ? typesRes.value : []
  challenges.value = challengesRes.status === 'fulfilled' ? challengesRes.value : []
  myRecords.value = recordsRes.status === 'fulfilled' ? recordsRes.value : []

  if (typesRes.status === 'rejected' || challengesRes.status === 'rejected') {
    error.value =
      'Nao foi possivel carregar todos os desafios. Tente novamente mais tarde.'
  }

  loading.value = false
}

onMounted(loadData)

async function start(c: ChallengeType) {
  if (startingId.value !== null) return
  startingId.value = c.id
  error.value = null
  try {
    const record = await createChallengeUser({ challenge_id: c.id })
    router.push({ name: 'challenge-detail', params: { id: String(record.id) } })
  } catch {
    error.value = 'Nao foi possivel iniciar o desafio.'
    startingId.value = null
  }
}

function getTypeBadgeClass(color: string) {
  return `type-badge type-${color}`
}

function getStatus(r?: ChallengeUserType) {
  if (!r) return null
  return r.completed ? 'done' : 'progress'
}
</script>

<template>
  <main class="challenge-page">
    <div class="page-inner">
      <header class="page-header">
        <h1 class="page-title">Desafios</h1>
        <p class="page-sub">
          Escolha uma cifra e decifre as mensagens. Cada desafio registra seu progresso.
        </p>
      </header>

      <div v-if="loading" class="state-box">Carregando desafios...</div>

      <div v-else-if="error" class="feedback feedback-wrong state-box">{{ error }}</div>

      <template v-else>
        <section v-for="group in grouped" :key="group.id" class="type-section">
          <div class="type-header">
            <div class="type-header-left">
              <span :class="getTypeBadgeClass(group.color)">{{ group.name }}</span>
              <div class="type-stars">
                <svg
                  v-for="(filled, i) in Array.from({ length: 5 }, (_, i) => i < group.stars)"
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
            <span class="type-count">{{ group.challenges.length }} desafios</span>
          </div>
          <p class="type-desc">{{ group.description }}</p>

          <div class="challenge-list">
            <article v-for="c in group.challenges" :key="c.id" class="challenge-row">
              <div class="row-info">
                <div class="row-top">
                  <h3 class="row-title">{{ c.title }}</h3>
                  <span v-if="getStatus(recordFor(c.id)) === 'done'" class="status done">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                      <path
                        d="M8 0a8 8 0 1 1 0 16A8 8 0 0 1 8 0Zm3.28 5.22a.75.75 0 0 0-1.06 0L6.75 8.69 5.28 7.22a.75.75 0 0 0-1.06 1.06l2 2a.75.75 0 0 0 1.06 0l4-4a.75.75 0 0 0 0-1.06Z"
                      />
                      Concluido</svg
                    >
                  </span>
                  <span v-else-if="getStatus(recordFor(c.id)) === 'progress'" class="status progress"
                    >Em andamento</span
                  >
                </div>
                <p class="row-desc">{{ c.description }}</p>
              </div>
              <div class="row-meta">
                <span class="row-xp">{{ c.xp }} XP</span>
                <button
                  class="btn-start"
                  :disabled="startingId !== null"
                  @click="start(c)"
                >
                  {{ getStatus(recordFor(c.id)) ? 'Continuar' : 'Iniciar' }}
                  <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                    <path d="M6 4l4 4-4 4" />
                  </svg>
                </button>
              </div>
            </article>
          </div>
        </section>
      </template>
    </div>
  </main>
</template>

<style scoped>
.challenge-page {
  flex: 1;
  padding: 32px 24px;
}

.page-inner {
  max-width: 960px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 24px;
  animation: fadeIn 0.3s var(--ease-out);
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

.page-sub {
  font-size: 14px;
  color: var(--text-secondary);
}

.state-box {
  padding: 32px;
  text-align: center;
  font-size: 14px;
  color: var(--text-secondary);
}

.type-section {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}

.type-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 16px 20px;
}

.type-header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.type-count {
  font-size: 12px;
  color: var(--text-subtle);
  background: var(--bg-emphasis);
  padding: 2px 8px;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
  white-space: nowrap;
}

.type-badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 10px;
  font-size: 12px;
  font-weight: 600;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
  letter-spacing: 0.02em;
}

.type-green {
  background: var(--accent-green-muted);
  color: var(--accent-green);
  border: 1px solid rgba(35, 134, 54, 0.3);
}

.type-yellow {
  background: var(--accent-yellow-muted);
  color: var(--accent-yellow);
  border: 1px solid rgba(187, 128, 9, 0.3);
}

.type-purple {
  background: var(--accent-purple-muted);
  color: var(--accent-purple);
  border: 1px solid rgba(163, 113, 247, 0.3);
}

.type-stars {
  display: flex;
  gap: 1px;
}

.type-desc {
  padding: 0 20px 14px;
  font-size: 13px;
  color: var(--text-secondary);
  line-height: 1.5;
  border-bottom: 1px solid var(--border-muted);
}

.challenge-list {
  display: flex;
  flex-direction: column;
}

.challenge-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-muted);
  transition: background 0.15s ease;
}

.challenge-row:last-child {
  border-bottom: none;
}

.challenge-row:hover {
  background: var(--bg-emphasis);
}

.row-info {
  min-width: 0;
}

.row-top {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 4px;
}

.row-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.status {
  font-size: 11px;
  font-weight: 600;
  font-family: var(--font-mono);
  padding: 1px 8px;
  border-radius: var(--radius-full);
  white-space: nowrap;
}

.status.done {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: var(--accent-green-muted);
  color: var(--accent-green);
  border: 1px solid rgba(35, 134, 54, 0.3);
}

.status.progress {
  background: var(--accent-yellow-muted);
  color: var(--accent-yellow);
  border: 1px solid rgba(187, 128, 9, 0.3);
}

.row-desc {
  font-size: 13px;
  color: var(--text-secondary);
  line-height: 1.45;
}

.row-meta {
  display: flex;
  align-items: center;
  gap: 14px;
  flex-shrink: 0;
}

.row-xp {
  font-size: 12px;
  font-weight: 600;
  color: var(--accent-yellow);
  font-family: var(--font-mono);
  white-space: nowrap;
}

.btn-start {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 7px 14px;
  font-size: 13px;
  font-weight: 600;
  font-family: var(--font-body);
  color: var(--text-on-emphasis);
  background: var(--accent-green-dark);
  border: 1px solid rgba(63, 185, 80, 0.4);
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: background 0.15s ease;
  white-space: nowrap;
}

.btn-start:hover:not(:disabled) {
  background: #2ea043;
}

.btn-start:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .challenge-page {
    padding: 20px 16px;
  }

  .challenge-row {
    flex-direction: column;
    align-items: stretch;
  }

  .row-meta {
    justify-content: space-between;
  }
}
</style>
