// frontend/src/features/auth/composables/useAuth.ts
import { ref, computed } from 'vue'
import type { UserType } from '../type/userType'

// a sessao vive em cookie httpOnly; aqui persistimos apenas o user (nao sensivel)
const user = ref<UserType | null>(
  JSON.parse(localStorage.getItem('user') ?? 'null')
)

export function useAuth() {
  const isLoggedIn = computed(() => !!user.value)

  function setAuth(newUser: UserType) {
    user.value = newUser
    localStorage.setItem('user', JSON.stringify(newUser))
  }

  function clearAuth() {
    user.value = null
    localStorage.removeItem('user')
  }

  return { user, isLoggedIn, setAuth, clearAuth }
}