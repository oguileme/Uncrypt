export interface ChallengeUserType {
  id: number
  challenge_id: number
  user_id: number
  attempts: number
  is_completed: boolean
  time_taken: number
}