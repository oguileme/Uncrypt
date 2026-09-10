import { api } from '@/config/api/api'
import type { Feedback, FeedbackPayload } from '../types/typeFeedback'

export async function submitFeedback(payload: FeedbackPayload): Promise<Feedback> {
  const response = await api.post('/feedback', payload)
  return response.data
}