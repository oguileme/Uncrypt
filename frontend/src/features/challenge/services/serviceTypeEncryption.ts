import { api } from '@/config/api/api'
import type { TypeEncryptionType } from '../types/typeTypeEncryption'

export async function getTypeEncryptions(): Promise<TypeEncryptionType[]> {
  const response = await api.get('/type-encryptions')
  return response.data
}

export async function getTypeEncryptionById(id: number): Promise<TypeEncryptionType> {
  const response = await api.get(`/type-encryptions/${id}`)
  return response.data
}

export async function createTypeEncryption(data: Omit<TypeEncryptionType, 'id'>): Promise<TypeEncryptionType> {
  const response = await api.post('/type-encryptions', data)
  return response.data
}

export async function updateTypeEncryption(id: number, data: Partial<Omit<TypeEncryptionType, 'id'>>): Promise<TypeEncryptionType> {
  const response = await api.put(`/type-encryptions/${id}`, data)
  return response.data
}

export async function deleteTypeEncryption(id: number): Promise<void> {
  await api.delete(`/type-encryptions/${id}`)
}