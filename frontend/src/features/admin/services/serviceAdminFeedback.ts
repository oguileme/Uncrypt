import { api } from '@/config/api/api'
import type { AdminFeedback, AdminFeedbackFilters, AdminFeedbackPage } from '../types/typeAdminFeedback'
import type { FeedbackStatus } from '@/features/feedback/types/typeFeedback'

export async function getAdminFeedbacks(page = 1, filters: AdminFeedbackFilters = {}, perPage = 15): Promise<AdminFeedbackPage> {
  const params: Record<string, unknown> = { page, per_page: perPage }
  if (filters.status) params.status = filters.status
  if (filters.feedback_type) params.feedback_type = filters.feedback_type
  const response = await api.get('/feedback', { params })
  return response.data
}

export async function updateFeedbackStatus(id: number, status: FeedbackStatus): Promise<AdminFeedback> {
  const response = await api.patch(`/feedback/${id}`, { status })
  return response.data
}

export async function deleteFeedback(id: number): Promise<void> {
  await api.delete(`/feedback/${id}`)
}