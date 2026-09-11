import { api } from '@/config/api/api'
import type { HistoryPageData } from '../types/typeHistory'

export async function getHistory(page = 1, perPage = 15): Promise<HistoryPageData> {
  const response = await api.get('/user/history', { params: { page, per_page: perPage } })
  return response.data
}