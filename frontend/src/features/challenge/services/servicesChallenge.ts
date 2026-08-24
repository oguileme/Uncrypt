import { api } from '@/config/api/api'
import type { ChallengeType } from '../types/typeChallenge'

export async function getChallenges(): Promise<ChallengeType[]> {
  const response = await api.get('/challenges')
  return response.data
}

export async function getRecommendedChallenges(): Promise<ChallengeType[]> {
  const response = await api.get('/challenge/recommendations')
  return response.data
}

export async function getChallengeById(id: number): Promise<ChallengeType> {
  const response = await api.get(`/challenges/${id}`)
  return response.data
}

export async function createChallenge(data: Omit<ChallengeType, 'id'>): Promise<ChallengeType> {
  const response = await api.post('/challenges', data)
  return response.data
}

export async function updateChallenge(id: number, data: Partial<Omit<ChallengeType, 'id'>>): Promise<ChallengeType> {
  const response = await api.put(`/challenges/${id}`, data)
  return response.data
}

export async function deleteChallenge(id: number): Promise<void> {
  await api.delete(`/challenges/${id}`)
}