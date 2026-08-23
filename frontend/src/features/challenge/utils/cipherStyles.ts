export const typeColorMap: Record<string, string> = {
  'Cifra de Cesar': 'green',
  ROT13: 'yellow',
  Base64: 'purple',
}

export function getTypeColor(name: string): string {
  return typeColorMap[name] ?? 'green'
}

const difficultyStarsMap: Record<string, number> = {
  easy: 1,
  medium: 3,
  hard: 5,
}

export function difficultyToStars(difficulty: string): number {
  return difficultyStarsMap[difficulty] ?? 1
}
