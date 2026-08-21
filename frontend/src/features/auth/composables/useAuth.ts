// frontend/src/features/auth/composables/useAuth.ts
import { ref, computed } from 'vue'
import type { UserType } from '../type/userType'

const user = ref<UserType | null>(
  JSON.parse(localStorage.getItem('user') ?? 'null')
)
const token = ref<string | null>(localStorage.getItem('token'))

export function useAuth() {
  const isLoggedIn = computed(() => !!token.value)

  function setAuth(newUser: UserType, newToken: string) {
    user.value = newUser
    token.value = newToken
    localStorage.setItem('user', JSON.stringify(newUser))
    localStorage.setItem('token', newToken)
  }

  function clearAuth() {
    user.value = null
    token.value = null
    localStorage.removeItem('user')
    localStorage.removeItem('token')
  }

  return { user, token, isLoggedIn, setAuth, clearAuth }
}