import { ref } from 'vue'

export type ThemeKind = 'dark' | 'light'

const STORAGE_KEY = 'uncrypt-theme'

function initialTheme(): ThemeKind {
  const stored = localStorage.getItem(STORAGE_KEY)
  if (stored === 'dark' || stored === 'light') return stored
  return window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark'
}

function applyTheme(theme: ThemeKind) {
  document.documentElement.dataset.theme = theme
}

const theme = ref<ThemeKind>(initialTheme())
applyTheme(theme.value)

export function useTheme() {
  function setTheme(next: ThemeKind) {
    theme.value = next
    localStorage.setItem(STORAGE_KEY, next)
    applyTheme(next)
  }

  function toggleTheme() {
    setTheme(theme.value === 'dark' ? 'light' : 'dark')
  }

  return { theme, setTheme, toggleTheme }
}