<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'
import { getChallenges, createChallenge, updateChallenge, deleteChallenge } from '@/features/challenge/services/servicesChallenge'
import { getTypeEncryptions } from '@/features/challenge/services/serviceTypeEncryption'
import type { ChallengeType } from '@/features/challenge/types/typeChallenge'
import type { TypeEncryptionType } from '@/features/challenge/types/typeTypeEncryption'

const emit = defineEmits<{ forbidden: [] }>()

const items = ref<ChallengeType[]>([])
const types = ref<TypeEncryptionType[]>([])
const loading = ref(true)
const error = ref(false)
const busyId = ref<number | null>(null)
const showCreate = ref(false)
const creating = ref(false)
const createError = ref('')

const form = ref({
  title: '',
  description: '',
  type_encryption_id: 0,
  phrase: '',
  key: '',
  hint: '',
  xp: 100,
  is_active: true,
})

function isForbidden(e: unknown): boolean {
  return axios.isAxiosError(e) && e.response?.status === 403
}

async function loadAll() {
  loading.value = true
  error.value = false
  try {
    const [challengesRes, typesRes] = await Promise.allSettled([getChallenges(), getTypeEncryptions()])
    if (challengesRes.status === 'fulfilled') items.value = challengesRes.value
    if (typesRes.status === 'fulfilled') types.value = typesRes.value
    if (challengesRes.status === 'rejected' && isForbidden(challengesRes.reason)) {
      emit('forbidden')
      return
    }
  } catch (e) {
    if (isForbidden(e)) {
      emit('forbidden')
      return
    }
    error.value = true
  } finally {
    loading.value = false
  }
}

async function toggleActive(challenge: ChallengeType) {
  busyId.value = challenge.id
  try {
    await updateChallenge(challenge.id, { is_active: !challenge.is_active })
    challenge.is_active = !challenge.is_active
  } catch (e) {
    if (isForbidden(e)) {
      emit('forbidden')
      return
    }
  } finally {
    busyId.value = null
  }
}

async function remove(challenge: ChallengeType) {
  const confirmed = window.confirm(`Excluir desafio "${challenge.title}"?`)
  if (!confirmed) return
  busyId.value = challenge.id
  try {
    await deleteChallenge(challenge.id)
    items.value = items.value.filter((c) => c.id !== challenge.id)
  } catch (e) {
    if (isForbidden(e)) {
      emit('forbidden')
      return
    }
  } finally {
    busyId.value = null
  }
}

function resetCreate() {
  form.value = { title: '', description: '', type_encryption_id: types.value[0]?.id ?? 0, phrase: '', key: '', hint: '', xp: 100, is_active: true }
  createError.value = ''
  showCreate.value = true
}

async function submitCreate() {
  creating.value = true
  createError.value = ''
  try {
    const created = await createChallenge(form.value)
    items.value.unshift(created)
    showCreate.value = false
  } catch (e) {
    if (isForbidden(e)) {
      emit('forbidden')
      return
    }
    createError.value = 'Verifique os campos e tente novamente.'
  } finally {
    creating.value = false
  }
}

onMounted(() => loadAll())
</script>

<template>
  <section class="admin-section">
    <header class="section-header">
      <div>
        <h2 class="section-title">Desafios</h2>
        <p class="section-sub">{{ items.length }} registro{{ items.length === 1 ? '' : 's' }}</p>
      </div>
      <button class="btn btn-primary" type="button" @click="resetCreate">Novo desafio</button>
    </header>

    <div v-if="loading" class="state-box">Carregando desafios...</div>

    <div v-else-if="error" class="state-box">N&atilde;o foi poss&iacute;vel carregar os desafios.</div>

    <template v-else>
      <form v-if="showCreate" class="create-form" @submit.prevent="submitCreate">
        <div class="form-row">
          <label class="filter-label">
            <span class="filter-name">T&iacute;tulo</span>
            <input v-model="form.title" class="filter-input" required />
          </label>
          <label class="filter-label">
            <span class="filter-name">Descri&ccedil;&atilde;o</span>
            <input v-model="form.description" class="filter-input" required />
          </label>
          <label class="filter-label">
            <span class="filter-name">Tipo</span>
            <select v-model.number="form.type_encryption_id" class="filter-select" required>
              <option v-for="t in types" :key="t.id" :value="t.id">{{ t.name }}</option>
            </select>
          </label>
        </div>
        <div class="form-row">
          <label class="filter-label">
            <span class="filter-name">Frase</span>
            <input v-model="form.phrase" class="filter-input" required />
          </label>
          <label class="filter-label">
            <span class="filter-name">Chave</span>
            <input v-model="form.key" class="filter-input" />
          </label>
          <label class="filter-label">
            <span class="filter-name">Dica</span>
            <input v-model="form.hint" class="filter-input" required />
          </label>
        </div>
        <div class="form-row form-row-end">
          <label class="filter-label">
            <span class="filter-name">XP</span>
            <input v-model.number="form.xp" type="number" min="0" class="filter-input filter-input-sm" required />
          </label>
          <label class="form-check">
            <input v-model="form.is_active" type="checkbox" />
            Ativo
          </label>
          <div class="form-actions">
            <button class="btn btn-outline" type="button" @click="showCreate = false">Cancelar</button>
            <button class="btn btn-primary" type="submit" :disabled="creating">
              <span v-if="creating" class="btn-spinner" aria-hidden="true"></span>
              Criar
            </button>
          </div>
          <p v-if="createError" class="form-error">{{ createError }}</p>
        </div>
      </form>

      <ul v-if="items.length" class="fb-list">
        <li v-for="challenge in items" :key="challenge.id" class="fb-row" :class="{ busy: busyId === challenge.id }">
          <div class="fb-main">
            <h3 class="fb-title">{{ challenge.title }}</h3>
            <p class="fb-text">{{ challenge.description }}</p>
            <div class="fb-meta">
              <span class="fb-type fb-type-general">{{ challenge.type_encryption?.name ?? '?' }}</span>
              <span class="fb-date">XP {{ challenge.xp }}</span>
              <span class="fb-date">{{ challenge.is_active ? 'Ativo' : 'Inativo' }}</span>
            </div>
          </div>
          <div class="fb-actions">
            <button
              class="btn"
              :class="challenge.is_active ? 'btn-outline' : 'btn-primary'"
              type="button"
              :disabled="busyId === challenge.id"
              @click="toggleActive(challenge)"
            >
              {{ challenge.is_active ? 'Desativar' : 'Ativar' }}
            </button>
            <button class="btn btn-danger" type="button" :disabled="busyId === challenge.id" @click="remove(challenge)">
              Excluir
            </button>
          </div>
        </li>
      </ul>

      <div v-else class="state-box">Nenhum desafio encontrado.</div>
    </template>
  </section>
</template>

<style scoped>
.admin-section {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-lg);
  padding: 20px;
  animation: fadeIn 0.4s var(--ease-out);
}

.section-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.section-title {
  margin: 0;
  font-size: 17px;
  font-weight: 600;
  color: var(--text-primary);
}

.section-sub {
  margin: 2px 0 0;
  font-size: 13px;
  color: var(--text-secondary);
}

.state-box {
  background: var(--bg-inset);
  border: 1px dashed var(--border);
  border-radius: var(--radius-md);
  padding: 32px 20px;
  text-align: center;
  font-size: 13px;
  color: var(--text-secondary);
}

.create-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
  padding: 16px;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  margin-bottom: 16px;
}

.form-row {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.form-row-end {
  align-items: flex-end;
}

.filter-label {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 120px;
}

.filter-name {
  font-size: 11px;
  color: var(--text-subtle);
  font-family: var(--font-mono);
}

.filter-input,
.filter-select {
  padding: 6px 10px;
  font-size: 13px;
  color: var(--text-primary);
  background: var(--bg-inset);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  font-family: var(--font-body);
}

.filter-input-sm {
  width: 80px;
}

.form-check {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 13px;
  color: var(--text-primary);
  padding-bottom: 6px;
}

.form-actions {
  display: flex;
  gap: 8px;
  margin-left: auto;
}

.form-error {
  width: 100%;
  margin: 4px 0 0;
  font-size: 13px;
  color: var(--accent-red);
}

.fb-list {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.fb-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 16px;
  padding: 14px;
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  transition: border-color 0.15s ease, opacity 0.15s ease;
}

.fb-row:hover {
  border-color: var(--border-emphasis);
}

.fb-row.busy {
  opacity: 0.55;
}

.fb-main {
  min-width: 0;
}

.fb-title {
  margin: 0;
  font-size: 15px;
  font-weight: 600;
  color: var(--text-primary);
}

.fb-text {
  margin: 6px 0;
  font-size: 14px;
  color: var(--text-secondary);
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.fb-meta {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.fb-type {
  font-size: 11px;
  font-weight: 600;
  padding: 2px 8px;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
}

.fb-type-general {
  color: var(--accent-purple);
  background: var(--accent-purple-muted);
}

.fb-date {
  font-size: 12px;
  color: var(--text-subtle);
  font-family: var(--font-mono);
}

.fb-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 6px 14px;
  font-size: 13px;
  font-weight: 500;
  border-radius: var(--radius-md);
  border: 1px solid transparent;
  cursor: pointer;
  font-family: var(--font-body);
  transition: background 0.15s ease, border-color 0.15s ease;
}

.btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-outline {
  color: var(--text-primary);
  background: transparent;
  border-color: var(--border);
}

.btn-outline:hover:not(:disabled) {
  background: var(--bg-emphasis);
  border-color: var(--border-emphasis);
}

.btn-primary {
  color: var(--text-on-emphasis);
  background: var(--accent-green-dark);
  border-color: rgba(63, 185, 80, 0.35);
}

.btn-primary:hover:not(:disabled) {
  background: var(--accent-green-hover);
}

.btn-danger {
  color: var(--accent-red);
  background: transparent;
  border-color: var(--border);
}

.btn-danger:hover:not(:disabled) {
  background: var(--accent-red-muted);
  border-color: var(--accent-red);
}

.btn-spinner {
  width: 12px;
  height: 12px;
  border: 2px solid rgba(255, 255, 255, 0.4);
  border-top-color: #fff;
  border-radius: 50%;
  display: inline-block;
  animation: spin 0.6s linear infinite;
  margin-right: 6px;
  vertical-align: -1px;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

@media (max-width: 640px) {
  .fb-row {
    flex-direction: column;
  }
  .fb-actions {
    width: 100%;
    justify-content: flex-end;
  }
}
</style>