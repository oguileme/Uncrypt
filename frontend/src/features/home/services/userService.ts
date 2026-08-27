import { api } from '@/config/api/api'
import type { UserType } from '@/features/auth/type/userType'

export interface UserMetricsType {
  challenges_completed: number
  accuracy_rate: number
  avg_time_per_challenge: number
}

export interface RecentActivityType {
  id: number
  challenge: string
  result: 'correct' | 'wrong'
  time: string
  attempts: number
}

export async function getMe(): Promise<UserType> {
  const response = await api.get('/user')
  return response.data
}

export async function getUserMetrics(): Promise<UserMetricsType> {
  const response = await api.get('/user/metrics')
  return response.data
}

export async function getRecentActivity(limit = 5): Promise<RecentActivityType[]> {
  const response = await api.get('/user/recent-activity', { params: { limit } })
  return response.data
}
