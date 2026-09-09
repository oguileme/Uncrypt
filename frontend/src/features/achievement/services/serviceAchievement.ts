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