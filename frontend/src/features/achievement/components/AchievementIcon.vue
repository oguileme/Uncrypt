<script setup lang="ts">
import type { AchievementIconKind } from '../types/typeAchievement'

defineProps<{
  kind: AchievementIconKind
  size?: number
}>()
</script>

<template>
  <svg
    :width="size ?? 26"
    :height="size ?? 26"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="1.6"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
  >
    <!-- Chave (primeira cifra decifrada) -->
    <template v-if="kind === 'key'">
      <circle cx="7.5" cy="15.5" r="4.5" />
      <path d="M10.7 12.3 21 2" />
      <path d="M16 3l3 3" />
      <path d="M13 6l3 3" />
    </template>

    <!-- Rotor cilíndrico em rotação -->
    <template v-else-if="kind === 'rotor'">
      <circle cx="12" cy="12" r="8.5" />
      <circle cx="12" cy="12" r="2" />
      <path d="M12 3.5v-2M12 22.5v-2M3.5 12h-2M22.5 12h-2" />
      <path d="M6 12a6 6 0 0 1 6-6" />
      <path d="M12 18a6 6 0 0 0 6-6" />
    </template>

    <!-- Engrenagem de precisão -->
    <template v-else-if="kind === 'gear'">
      <circle cx="12" cy="12" r="3" />
      <path d="M12 2.5v2.2M12 19.3v2.2M2.5 12h2.2M19.3 12h2.2M5.3 5.3l1.6 1.6M17.1 17.1l1.6 1.6M18.7 5.3l-1.6 1.6M6.9 17.1l-1.6 1.6" />
    </template>

    <!-- Onda de Morse/transmissão -->
    <template v-else-if="kind === 'wave'">
      <path d="M3 12c2.5-4 5.5-4 8 0s5.5 4 8 0" />
      <path d="M1 17c2-3 4-3 6 0s4 3 6 0 4-3 6 0" opacity="0.55" />
      <line x1="12" y1="3" x2="12" y2="6" />
      <line x1="9" y1="4.5" x2="9" y2="6" />
      <line x1="15" y1="4.5" x2="15" y2="6" />
    </template>

    <!-- Cofre / fechadura intacta -->
    <template v-else-if="kind === 'lock'">
      <rect x="5" y="11" width="14" height="10" rx="2" />
      <path d="M8 11V7a4 4 0 0 1 8 0v4" />
      <circle cx="12" cy="16" r="1.5" />
    </template>

    <!-- Estrela do criptógrafo -->
    <template v-else-if="kind === 'star'">
      <path d="M12 3l2.4 4.9 5.4.8-3.9 3.8.9 5.4-4.8-2.5-4.8 2.5.9-5.4L4.2 8.7l5.4-.8z" />
    </template>

    <!-- Cofre de coleção / baú -->
    <template v-else-if="kind === 'vault'">
      <path d="M4 8h16v11a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1z" />
      <path d="M4 12h16" />
      <circle cx="15.5" cy="15.5" r="1.5" />
      <path d="M8 8.5V7a4 4 0 0 1 8 0v1.5" />
    </template>

    <!-- Bússola / guardião -->
    <template v-else-if="kind === 'compass'">
      <circle cx="12" cy="12" r="8.5" />
      <path d="M15.5 8.5 13.5 13.5 8.5 15.5l2-5z" />
    </template>
  </svg>
</template>
