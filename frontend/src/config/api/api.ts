import axios from "axios";
import { useAuth } from '@/features/auth/composables/useAuth'
import router from '@/router'

export const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
    headers:{
        'Content-Type': 'application/json',
    }
})

api.interceptors.request.use((config) =>{
    const token = localStorage.getItem('token');
    if(token){
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
})

api.interceptors.response.use(
    (response) => response,
    async (error) => {
        // token invalido/expirado: desloga e manda pro login,
        // exceto quando o proprio login/register falhou (credenciais erradas)
        if (axios.isAxiosError(error) && error.response?.status === 401) {
            const url = error.config?.url ?? ''
            const isAuthRequest = url.includes('/login') || url.includes('/register')

            if (!isAuthRequest) {
                const { clearAuth } = useAuth()
                clearAuth()
                if (router.currentRoute.value.name !== 'login') {
                    await router.push({ name: 'login' })
                }
            }
        }
        return Promise.reject(error)
    }
)