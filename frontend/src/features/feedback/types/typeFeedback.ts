export type FeedbackType = 'bug' | 'feature_request' | 'general'

export type FeedbackStatus = 'new' | 'in_progress' | 'resolved'

export interface FeedbackPayload {
  user_id: number
  context_url: string
  feedback_text: string
  feedback_type: FeedbackType
}

export interface Feedback {
  id: number
  user_id: number
  context_url: string
  feedback_text: string
  feedback_type: FeedbackType
  status: FeedbackStatus
  created_at: string
  updated_at: string
}