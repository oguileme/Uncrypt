import type { Feedback, FeedbackStatus, FeedbackType } from '@/features/feedback/types/typeFeedback'

export interface AdminFeedbackUser {
  id: number
  name: string
  username: string
  email: string
}

export interface AdminFeedback extends Feedback {
  user?: AdminFeedbackUser
}

export interface AdminFeedbackFilters {
  status?: FeedbackStatus
  feedback_type?: FeedbackType
}

export interface AdminFeedbackPage {
  data: AdminFeedback[]
  current_page: number
  last_page: number
  total: number
  per_page: number
}

export const FEEDBACK_STATUS_OPTIONS: FeedbackStatus[] = ['new', 'in_progress', 'resolved']

export const FEEDBACK_TYPE_OPTIONS: FeedbackType[] = ['bug', 'feature_request', 'general']

export const FEEDBACK_STATUS_LABELS: Record<FeedbackStatus, string> = {
  new: 'Novo',
  in_progress: 'Em andamento',
  resolved: 'Resolvido',
}

export const FEEDBACK_TYPE_LABELS: Record<FeedbackType, string> = {
  bug: 'Bug',
  feature_request: 'Nova proposta',
  general: 'Geral',
}