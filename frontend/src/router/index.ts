import { createRouter, createWebHistory } from 'vue-router'

import { useAuth } from '@/features/auth/composables/useAuth'

declare module 'vue-router' {
  interface RouteMeta {
    requiresAuth?: boolean
    requiresAdmin?: boolean
  }
}

const publicRoutes = ['landing', 'login', 'register', 'not-found']

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),

  routes: [
    {
      path: '/',
      name: 'landing',
      component: () => import('../features/landing/LandingPage.vue'),
    },

    {
      path: '/login',
      name: 'login',
      component: () => import('../features/auth/views/LoginPage.vue'),
    },

    {
      path: '/register',
      name: 'register',
      component: () => import('../features/auth/views/RegisterPage.vue'),
    },

    {
      path: '/challenge',
      name: 'challenge',
      component: () => import('../features/challenge/views/ChallengePage.vue'),
    },

    {
      path: '/home',
      name: 'home',
      component: () => import('../features/home/HomePage.vue'),
    },

    {
      path: '/profile',
      name: 'profile',
      component: () => import('../features/auth/views/UserPage.vue'),
    },

    {
      path: '/settings',
      name: 'settings',
      component: () => import('../features/settings/views/SettingsPage.vue'),
    },

    {
      path: '/achievements',
      name: 'achievements',
      component: () => import('../features/achievement/views/AchievementsPage.vue'),
    },

    {
      path: '/ranking',
      name: 'ranking',
      component: () => import('../features/ranking/views/RankingPage.vue'),
    },

    {
      path: '/history',
      name: 'history',
      component: () => import('../features/history/views/HistoryPage.vue'),
    },

    {
      path: '/challenge/:id',
      name: 'challenge-detail',
      component: () => import('../features/challenge/views/ChallengeUser.vue'),
    },

    {
      path: '/admin',
      name: 'admin',
      component: () => import('../features/admin/views/AdminPage.vue'),
      meta: { requiresAdmin: true },
    },

    {
      path: '/403',
      name: 'forbidden',
      component: () => import('../features/error/views/ForbiddenPage.vue'),
    },

    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('../features/error/views/NotFoundPage.vue'),
    },
  ],
})

router.beforeEach((to) => {
  const { isLoggedIn, user } = useAuth()

  if (!publicRoutes.includes(to.name as string) && !isLoggedIn.value) {
    return { name: 'login' }
  }

  // camada de UX: o backend ainda exige o middleware admin; aqui evitamos
  // flash de conteudo para quem esta logado mas nao e administrador
  if (to.meta.requiresAdmin && !user.value?.is_admin) {
    return { name: 'forbidden' }
  }
})

export default router