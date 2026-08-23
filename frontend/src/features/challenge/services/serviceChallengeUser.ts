import axios from 'axios'
import { api } from '@/config/api/api'
import type { ChallengeUserType } from '../types/typeChallangeUser'

export interface AttemptResponse {
  message: string
  completed: boolean
  xp_gained?: number
  time_taken?: number
  challenge_user?: ChallengeUserType
}



export async function getChallengeUsers(): Promise<ChallengeUserType[]> {
  const response = await api.get('/challenge-users')
  return response.data
}

export async function getChallengeUserById(id: number): Promise<ChallengeUserType> {
  const response = await api.get(`/challenge-users/${id}`)
  return response.data
}

export async function createChallengeUser(data: { challenge_id: number }): Promise<ChallengeUserType> {
  const response = await api.post('/challenge-users', data)
  return response.data
}

export async function updateChallengeUser(id: number, data: Partial<Omit<ChallengeUserType, 'id'>>): Promise<ChallengeUserType> {
  const response = await api.put(`/challenge-users/${id}`, data)
  return response.data
}

export async function deleteChallengeUser(id: number): Promise<void> {
  await api.delete(`/challenge-users/${id}`)
}

export async function attemptChallengeUser(id: number, attempt: string): Promise<AttemptResponse> {
    try {
        const response = await api.post(`/challenge-users/${id}/attempt`, { attempt })
        return response.data
    } catch (error) {
        // 400 = tentativa errada (resposta esperada no fluxo), outros erros propagam
        if (axios.isAxiosError(error) && error.response?.status === 400) {
            return error.response.data as AttemptResponse
        }
        throw error
    }
}

export async function setHintUsed(id: number): Promise<ChallengeUserType> {
    const response = await api.put(`/challenge-users/${id}`, { hint_used: true })
    return response.data
}