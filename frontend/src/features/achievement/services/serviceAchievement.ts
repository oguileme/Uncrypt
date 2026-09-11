import { api } from '@/config/api/api'
import type { Achievement, AchievementProgressUser } from '../types/typeAchievement'

export async function getAchievements(): Promise<Achievement[]> {
  const response = await api.get('/achievement')
  return response.data
}

export async function getMyAchievementProgress(): Promise<AchievementProgressUser[]> {
  const response = await api.get('/achievement-progress')
  return response.data
}

export async function createAchievement(data: Omit<Achievement, 'id'>): Promise<Achievement> {
  const response = await api.post('/achievement', data)
  return response.data
}

export async function updateAchievement(id: number, data: Partial<Omit<Achievement, 'id'>>): Promise<Achievement> {
  const response = await api.put(`/achievement/${id}`, data)
  return response.data
}

export async function deleteAchievement(id: number): Promise<void> {
  await api.delete(`/achievement/${id}`)
}