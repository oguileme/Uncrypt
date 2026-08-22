import { api } from '@/config/api/api'
import type { ChallengeUserType } from '../types/typeChallangeUser'



export async function getChallengeUsers(): Promise<ChallengeUserType[]> {
  const response = await api.get('/challenge-users')
  return response.data
}

export async function getChallengeUserById(id: number): Promise<ChallengeUserType> {
  const response = await api.get(`/challenge-users/${id}`)
  return response.data
}

export async function createChallengeUser(data: Omit<ChallengeUserType, 'id'>): Promise<ChallengeUserType> {
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

export async function attemptChallengeUser(id: number, attempt: string): Promise<ChallengeUserType> {
    const response = await api.post(`/challenge-users/${id}/attempt`, { attempt })
    return response.data
}