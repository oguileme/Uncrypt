import { api } from '@/config/api/api'
import type { TypeEncryptionType } from '../types/typeTypeEncryption'

export async function getTypeEncryptions(): Promise<TypeEncryptionType[]> {
  const response = await api.get('/type-encryption')
  return response.data
}

export async function getTypeEncryptionById(id: number): Promise<TypeEncryptionType> {
  const response = await api.get(`/type-encryption/${id}`)
  return response.data
}

export async function createTypeEncryption(data: Omit<TypeEncryptionType, 'id'>): Promise<TypeEncryptionType> {
  const response = await api.post('/type-encryption', data)
  return response.data
}

export async function updateTypeEncryption(id: number, data: Partial<Omit<TypeEncryptionType, 'id'>>): Promise<TypeEncryptionType> {
  const response = await api.put(`/type-encryption/${id}`, data)
  return response.data
}

export async function deleteTypeEncryption(id: number): Promise<void> {
  await api.delete(`/type-encryption/${id}`)
}