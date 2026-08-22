import { createRouter, createWebHistory } from 'vue-router'

import { useAuth } from '@/features/auth/composables/useAuth'

const publicRoutes = ['landing', 'login', 'register']

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
      path: '/Challenge/:id',
      name: 'challenge-detail',
      component: () => import('../features/challenge/views/ChallengeUser.vue'),
    }
  ],
})

router.beforeEach((to) => {
  const { isLoggedIn } = useAuth()

  if (!publicRoutes.includes(to.name as string) && !isLoggedIn.value) {
    return { name: 'login' }
  }
})

export default router