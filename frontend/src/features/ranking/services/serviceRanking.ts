import { api } from '@/config/api/api'
import type { RankingEntry } from '../types/typeRanking'

export async function getRanking(limit = 10): Promise<RankingEntry[]> {
  const response = await api.get('/ranking', { params: { limit } })
  return response.data
}