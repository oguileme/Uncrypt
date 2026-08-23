import type { TypeEncryptionType } from './typeTypeEncryption'

export interface ChallengeType {
  id: number
  title: string
  description: string
  type_encryption_id: number
  phrase?: string
  key: string
  xp: number
  is_active: boolean
  hint: string
  ciphertext?: string
  type_encryption?: TypeEncryptionType
}
