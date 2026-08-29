export type AchievementIconKind =
  | 'key'
  | 'rotor'
  | 'gear'
  | 'wave'
  | 'lock'
  | 'star'
  | 'vault'
  | 'compass'

export interface AchievementProgressUser {
  user_id: number
  achievement_id: number
  progress: number
  is_completed: boolean
}

export interface Achievement {
  id: number
  name: string
  description: string
  xp_reward: number
  required_count: number
  icon: AchievementIconKind
  color: 'green' | 'blue' | 'yellow' | 'purple' | 'orange'
}
