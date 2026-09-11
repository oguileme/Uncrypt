<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { useAuth } from '@/features/auth/composables/useAuth'
import { getMe } from '@/features/home/services/userService'
import AdminFeedbackSection from '../components/AdminFeedbackSection.vue'
import AdminChallengesSection from '../components/AdminChallengesSection.vue'
import AdminAchievementsSection from '../components/AdminAchievementsSection.vue'
import AdminCiphersSection from '../components/AdminCiphersSection.vue'
import AdminMetricsSection from '../components/AdminMetricsSection.vue'

type AdminTab = 'feedback' | 'challenges' | 'achievements' | 'ciphers' | 'metrics'

const router = useRouter()
const { user, setAuth } = useAuth()

const activeTab = ref<AdminTab>('feedback')
const refreshing = ref(true)

// o painel sempre refaz o GET /user: is_admin atualizado na sessao (nada de
// confiar no localStorage possivelmente stale), e o servidor da a palavra final
onMounted(async () => {
  try {
    const me = await getMe()
    setAuth(me)
    if (!me.is_admin) {
      router.replace({ name: 'forbidden' })
      return
    }
  } catch (e) {
    if (axios.isAxiosError(e) && e.response?.status === 403) {
      router.replace({ name: 'forbidden' })
      return
    }
  } finally {
    refreshing.value = false
  }
})

function handleForbidden() {
  router.push({ name: 'forbidden' })
}
</script>

<template>
  <main class="admin-page">
    <div class="page-inner">
      <header class="page-header">
        <h1 class="page-title">Administra&ccedil;&atilde;o</h1>
        <p class="page-sub">
          Gest&atilde;o do feedback e dos recursos da plataforma.
          <template v-if="user?.is_admin">Voc&ecirc; &eacute; <strong>admin</strong>.</template>
        </p>
      </header>

      <div v-if="refreshing" class="state-box">Verificando acesso...</div>

      <template v-else>
        <div v-if="user?.is_admin" class="segmented" role="tablist" aria-label="Seções de administração">
          <button
            type="button"
            role="tab"
            :aria-selected="activeTab === 'feedback'"
            class="segmented-option"
            :class="{ active: activeTab === 'feedback' }"
            @click="activeTab = 'feedback'"
          >
            Feedback
          </button>
          <button
            type="button"
            role="tab"
            :aria-selected="activeTab === 'challenges'"
            class="segmented-option"
            :class="{ active: activeTab === 'challenges' }"
            @click="activeTab = 'challenges'"
          >
            Desafios
          </button>
          <button
            type="button"
            role="tab"
            :aria-selected="activeTab === 'achievements'"
            class="segmented-option"
            :class="{ active: activeTab === 'achievements' }"
            @click="activeTab = 'achievements'"
          >
            Conquistas
          </button>
          <button
            type="button"
            role="tab"
            :aria-selected="activeTab === 'ciphers'"
            class="segmented-option"
            :class="{ active: activeTab === 'ciphers' }"
            @click="activeTab = 'ciphers'"
          >
            Cifras
          </button>
          <button
            type="button"
            role="tab"
            :aria-selected="activeTab === 'metrics'"
            class="segmented-option"
            :class="{ active: activeTab === 'metrics' }"
            @click="activeTab = 'metrics'"
          >
            M&eacute;tricas
          </button>
        </div>

        <div v-if="user?.is_admin" class="section-host">
          <AdminFeedbackSection v-if="activeTab === 'feedback'" @forbidden="handleForbidden" />
          <AdminChallengesSection v-else-if="activeTab === 'challenges'" @forbidden="handleForbidden" />
          <AdminAchievementsSection v-else-if="activeTab === 'achievements'" @forbidden="handleForbidden" />
          <AdminCiphersSection v-else-if="activeTab === 'ciphers'" @forbidden="handleForbidden" />
          <AdminMetricsSection v-else @forbidden="handleForbidden" />
        </div>

        <div v-else class="state-box">Seu acesso &eacute; restrito. Redirecionando...</div>
      </template>
    </div>
  </main>
</template>

<style scoped>
.admin-page {
  flex: 1;
  padding: 32px 24px;
}

.page-inner {
  max-width: 860px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 20px;
  animation: fadeIn 0.5s var(--ease-out);
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
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  padding: 32px 20px;
  text-align: center;
  font-size: 13px;
  color: var(--text-secondary);
}

.segmented {
  display: inline-flex;
  gap: 4px;
  padding: 4px;
  background: var(--bg-inset);
  border: 1px solid var(--border-muted);
  border-radius: var(--radius-md);
  align-self: flex-start;
  flex-wrap: wrap;
}

.segmented-option {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 16px;
  font-size: 13px;
  font-weight: 500;
  color: var(--text-secondary);
  background: transparent;
  border: 1px solid transparent;
  border-radius: var(--radius-sm);
  cursor: pointer;
  transition: background 0.15s ease, color 0.15s ease;
  font-family: var(--font-body);
}

.segmented-option.active {
  background: var(--bg-overlay);
  color: var(--text-primary);
  box-shadow: var(--shadow-sm);
}

.segmented-option:hover:not(.active) {
  color: var(--text-primary);
}

.section-host {
  display: flex;
  flex-direction: column;
}

@media (max-width: 640px) {
  .admin-page {
    padding: 20px 16px;
  }
}
</style>