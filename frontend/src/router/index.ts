import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'landing',
      component: HomeView,
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
  ],
})

export default router
