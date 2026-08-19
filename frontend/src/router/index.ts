import { createRouter, createWebHistory } from 'vue-router'

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
      component: () => import('../features/challenge/ChallengePage.vue'),
    },
    {
      path: '/home',
      name: 'home',
      component: () => import('../features/home/HomePage.vue'),
    }
  ],
})

export default router
