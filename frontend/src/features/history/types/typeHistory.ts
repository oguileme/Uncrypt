export interface HistoryEntry {
  id: number
  challenge: string
  type: string | null
  xp: number | null
  completed: boolean
  attempts: number
  hint_used: boolean
  time_taken: number | null
  concluded_at: string | null
}

export interface HistoryPageData {
  data: HistoryEntry[]
  current_page: number
  last_page: number
  total: number
  per_page: number
}