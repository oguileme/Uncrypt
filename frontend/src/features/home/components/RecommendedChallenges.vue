<script setup lang="ts">
const challenges = [
  {
    id: 2,
    type: 'Cifra de César',
    typeColor: 'green',
    difficulty: 1,
    title: 'O Clássico',
    description: 'Cada letra foi deslocada no alfabeto por um número fixo.',
  },
  {
    id: 5,
    type: 'Vigenère',
    typeColor: 'purple',
    difficulty: 3,
    title: 'O Inquebrável',
    description: 'Uma palavra-chave aplica deslocamentos diferentes em cada letra.',
  },
  {
    id: 3,
    type: 'Morse',
    typeColor: 'yellow',
    difficulty: 2,
    title: 'O Telégrafo',
    description: 'Pontos e traços formam uma mensagem de mais de 100 anos.',
  },
]

function getStars(difficulty: number) {
  return Array.from({ length: 5 }, (_, i) => i < difficulty)
}
</script>

<template>
  <div class="recommended">
    <div class="section-header">
      <h2 class="section-title">Desafios Recomendados</h2>
      <span class="section-badge">{{ challenges.length }}</span>
    </div>

    <div class="challenge-list">
      <RouterLink
        v-for="c in challenges"
        :key="c.id"
        to="/challenge"
        class="recommended-item"
      >
        <div class="recommended-top">
          <span :class="['type-badge', `type-${c.typeColor}`]">{{ c.type }}</span>
          <div class="recommended-stars">
            <svg
              v-for="(filled, i) in getStars(c.difficulty)"
              :key="i"
              width="12"
              height="12"
              viewBox="0 0 16 16"
              :fill="filled ? '#d29922' : 'none'"
              :stroke="filled ? '#d29922' : '#6e7681'"
              stroke-width="1.5"
            >
              <path d="M8 1.5l2 4 4.5.7-3.2 3.1.8 4.4L8 11.3l-4.1 2.4.8-4.4L1.5 6.2l4.5-.7z" />
            </svg>
          </div>
        </div>

        <h3 class="recommended-title">{{ c.title }}</h3>
        <p class="recommended-desc">{{ c.description }}</p>

        <div class="recommended-bottom">
          <span class="btn-start">Iniciar</span>
          <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M6 4l4 4-4 4" />
          </svg>
        </div>
      </RouterLink>
    </div>
  </div>
</template>

<style scoped>
.recommended {
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  overflow: hidden;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-muted);
}

.section-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.section-badge {
  font-size: 12px;
  color: var(--text-subtle);
  background: var(--bg-emphasis);
  padding: 2px 8px;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
}

.challenge-list {
  display: flex;
  flex-direction: column;
}

.recommended-item {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 16px 20px;
  border-bottom: 1px solid var(--border-muted);
  text-decoration: none;
  transition: background 0.15s ease;
}

.recommended-item:last-child {
  border-bottom: none;
}

.recommended-item:hover {
  background: var(--bg-emphasis);
  text-decoration: none;
}

.recommended-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.type-badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 8px;
  font-size: 11px;
  font-weight: 600;
  border-radius: var(--radius-full);
  font-family: var(--font-mono);
  letter-spacing: 0.02em;
}

.type-green {
  background: var(--accent-green-muted);
  color: var(--accent-green);
}

.type-yellow {
  background: var(--accent-yellow-muted);
  color: var(--accent-yellow);
}

.type-purple {
  background: var(--accent-purple-muted);
  color: var(--accent-purple);
}

.recommended-stars {
  display: flex;
  gap: 1px;
}

.recommended-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--text-primary);
}

.recommended-desc {
  font-size: 13px;
  color: var(--text-secondary);
  line-height: 1.4;
}

.recommended-bottom {
  display: flex;
  align-items: center;
  gap: 4px;
  margin-top: 4px;
}

.btn-start {
  font-size: 12px;
  font-weight: 500;
  color: var(--accent-blue);
  transition: color 0.15s ease;
}

.recommended-item:hover .btn-start {
  color: #58a6ff;
}

.recommended-item:hover svg {
  transform: translateX(2px);
  transition: transform 0.15s ease;
}
</style>
