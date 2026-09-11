import { api } from '@/config/api/api'
import type { SystemMetrics } from '../types/typeAdminMetrics'

export async function getAdminMetrics(): Promise<SystemMetrics> {
  const response = await api.get('/admin/metrics')
  return response.data
}