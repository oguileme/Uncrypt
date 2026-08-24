<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import StatsCards from './components/StatsCards.vue'
import RecommendedChallenges from './components/RecommendedChallenges.vue'
import RecentActivity from './components/RecentActivity.vue'
import { getMe, getUserMetrics } from './services/userService'
import type { UserMetricsType } from './services/userService'
import type { UserType } from '@/features/auth/type/userType'
import { useAuth } from '@/features/auth/composables/useAuth'

const { user: authUser } = useAuth()

const me = ref<UserType | null>(null)
const metrics = ref<UserMetricsType | null>(null)

const firstName = computed(() => {
  const name = me.value?.name ?? authUser.value?.name ?? ''
  return name.split(' ')[0] || 'criptógrafo'
})

const username = computed(() => me.value?.username ?? authUser.value?.username ?? '')

onMounted(async () => {
  // carregamento independente: se um falhar, o outro ainda aparece
  const [meRes, metricsRes] = await Promise.allSettled([getMe(), getUserMetrics()])
  if (meRes.status === 'fulfilled') me.value = meRes.value
  if (metricsRes.status === 'fulfilled') metrics.value = metricsRes.value
})
</script>

<template>
  <main class="home-page">
    <div class="home-inner">
      <div class="home-greeting">
        <h1 class="greeting-title">
          Bem-vindo de volta, <span class="greeting-name">{{ firstName }}</span>
        </h1>
        <p class="greeting-sub">
          <template v-if="username">@{{ username }} &middot; </template>Continue sua jornada de
          descobertas criptogr&aacute;ficas.
        </p>
      </div>

      <StatsCards :level="me?.level" :xp-progress="me?.xp_progress" :xp-levelup="me?.xp_levelup"
        :accuracy-rate="metrics?.accuracy_rate" :challenges-completed="metrics?.challenges_completed"
        :avg-time="metrics?.avg_time_per_challenge" />

      <div class="home-content">
        <div class="home-main">
          <RecommendedChallenges />
        </div>
        <aside class="home-sidebar">
          <RecentActivity />
        </aside>
      </div>
    </div>
  </main>
</template>

<style scoped>
.home-page {
  flex: 1;
  padding: 32px 24px;
}

.home-inner {
  max-width: 1280px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 28px;
  animation: fadeIn 0.5s var(--ease-out);
}

.home-greeting {
  margin-bottom: 4px;
}

.greeting-title {
  font-size: 24px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 4px;
}

.greeting-name {
  background: linear-gradient(135deg, var(--accent-green), var(--accent-blue));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.greeting-sub {
  font-size: 14px;
  color: var(--text-secondary);
}

.home-content {
  display: grid;
  grid-template-columns: 1fr 360px;
  gap: 20px;
}

@media (max-width: 1024px) {
  .home-content {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .home-page {
    padding: 20px 16px;
  }

  .greeting-title {
    font-size: 20px;
  }
}
</style>
