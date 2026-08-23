import type { ChallengeType } from './typeChallenge'

export interface ChallengeUserType {
  id: number
  challenge_id: number
  user_id: number
  attempts: number
  completed: boolean
  time_taken: number | null
  hint_used: boolean
  challenge?: ChallengeType
}
