<script setup lang="ts">
import StatsCards from './components/StatsCards.vue'
import RecommendedChallenges from './components/RecommendedChallenges.vue'
import RecentActivity from './components/RecentActivity.vue'
</script>

<template>
  <main class="home-page">
    <div class="home-inner">
      <div class="home-greeting">
        <h1 class="greeting-title">Bem-vindo de volta, <span class="greeting-name">Guilherme</span></h1>
        <p class="greeting-sub">Continue sua jornada de descobertas criptogr&aacute;ficas.</p>
      </div>

      <StatsCards />

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
