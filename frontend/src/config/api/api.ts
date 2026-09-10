import axios from 'axios'
import { useAuth } from '@/features/auth/composables/useAuth'
import router from '@/router'

const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

// o endpoint de CSRF do Sanctum fica fora do prefixo /api (ex.: /sanctum/csrf-cookie)
const CSRF_BASE = new URL('/', API_BASE).toString().replace(/\/$/, '')

export const api = axios.create({
  baseURL: API_BASE,
  withCredentials: true,
  xsrfCookieName: '',
  xsrfHeaderName: '',
  headers: {
    'Content-Type': 'application/json',
  },
})

let csrfPending: Promise<void> | null = null

function getCookie(name: string): string | null {
  const escaped = name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1')
  const match = document.cookie.match(new RegExp('(?:^|; )' + escaped + '=([^;]*)'))
  return match ? decodeURIComponent(match[1] ?? '') : null
}

// o primeiro POST exige o token CSRF: busca /sanctum/csrf-cookie (uma vez).
// com force=true a busca acontece mesmo com cookie existente (token stale/419).
async function ensureCsrfToken(force = false) {
  if (!force && getCookie('XSRF-TOKEN')) return
  csrfPending ??= api
    .get('/sanctum/csrf-cookie', { baseURL: CSRF_BASE })
    .then(() => {
      csrfPending = null
    })
    .catch((e) => {
      csrfPending = null
      throw e
    })
  return csrfPending
}

let redirectingToLogin = false

api.interceptors.request.use(async (config) => {
  const method = (config.method ?? 'get').toLowerCase()
  if (['post', 'put', 'patch', 'delete'].includes(method)) {
    await ensureCsrfToken()
    // injeta manualmente (com url-decode) para nao depender do auto-xsrf do axios
    const token = getCookie('XSRF-TOKEN')
    if (token) config.headers.set('X-XSRF-TOKEN', token)
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (!axios.isAxiosError(error)) {
      return Promise.reject(error)
    }

    const url = error.config?.url ?? ''
    const status = error.response?.status
    const isAuthRequest = url.includes('/login') || url.includes('/register')

    // CSRF expirado: renova o cookie e tenta a requisicao uma unica vez
    if (status === 419 && !(error.config as { _retry?: boolean })._retry) {
      ;(error.config as { _retry?: boolean })._retry = true
      await ensureCsrfToken(true)
      return api.request(error.config!)
    }

    // sessao invalida/expirada: desloga e volta para o login
    if (status === 401 && !isAuthRequest && !redirectingToLogin) {
      redirectingToLogin = true
      try {
        const { clearAuth } = useAuth()
        clearAuth()
        if (router.currentRoute.value.name !== 'login') {
          await router.push({ name: 'login' })
        }
      } finally {
        redirectingToLogin = false
      }
    }

    return Promise.reject(error)
  },
)