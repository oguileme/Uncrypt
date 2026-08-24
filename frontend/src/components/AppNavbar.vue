<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
import { logout } from '@/features/auth/services/authService'
import { useAuth } from '@/features/auth/composables/useAuth'

const router = useRouter()
const { clearAuth } = useAuth()

const menuOpen = ref(false)
const route = useRoute()
const dropdownOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const publicRoutes = ['/', '/login', '/register']
const isLoggedIn = computed(() => !publicRoutes.includes(route.path))

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value
}

function closeDropdown() {
  dropdownOpen.value = false
}

async function handleLogout() {
  closeDropdown()
  try { await logout() } catch {}
  clearAuth()
  router.push('/')
}

function handleClickOutside(e: MouseEvent) {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target as Node)) {
    dropdownOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
  <nav class="navbar">
    <div class="navbar-inner">
      <RouterLink to="/" class="navbar-brand">
        <img src="@/assets/uncrypt fonte sem fundo.png" alt="Uncrypt" class="navbar-logo" />
      </RouterLink>

      <button class="menu-toggle" @click="menuOpen = !menuOpen" :class="{ active: menuOpen }">
        <span></span>
        <span></span>
        <span></span>
      </button>

      <div class="navbar-links" :class="{ open: menuOpen }">
        <template v-if="isLoggedIn">
          <RouterLink
            to="/home"
            class="nav-link"
            :class="{ active: route.path === '/home' }"
            @click="menuOpen = false"
          >
            Home
          </RouterLink>
          <RouterLink
            to="/challenge"
            class="nav-link"
            :class="{ active: route.path === '/challenge' }"
            @click="menuOpen = false"
          >
            Desafios
          </RouterLink>
          <a href="#" class="nav-link" @click="menuOpen = false">Ranking</a>
        </template>
        <template v-else>
          <a href="#features" class="nav-link" @click="menuOpen = false">Funcionalidades</a>
          <a href="#how" class="nav-link" @click="menuOpen = false">Como Funciona</a>
        </template>

        <div class="navbar-actions">
          <template v-if="isLoggedIn">
            <div class="user-menu" ref="dropdownRef">
              <button class="user-trigger" @click.stop="toggleDropdown">
                <div class="user-avatar">GR</div>
                <span class="user-name">Guilherme</span>
                <svg
                  width="12"
                  height="12"
                  viewBox="0 0 12 12"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="1.5"
                  class="chevron"
                  :class="{ open: dropdownOpen }"
                >
                  <path d="M3 5l3 3 3-3" />
                </svg>
              </button>

              <Transition name="dropdown">
                <div v-if="dropdownOpen" class="dropdown-menu">
                  <RouterLink to="/profile" class="dropdown-item" @click="closeDropdown">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                      <circle cx="12" cy="7" r="4" />
                    </svg>
                    Meu Perfil
                  </RouterLink>
                  <a href="#" class="dropdown-item" @click="closeDropdown">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <circle cx="12" cy="12" r="3" />
                      <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />
                    </svg>
                    Configura&ccedil;&otilde;es
                  </a>
                  <div class="dropdown-divider"></div>
                  <button class="dropdown-item dropdown-item-danger" @click="handleLogout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                      <polyline points="16 17 21 12 16 7" />
                      <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    Sair
                  </button>
                </div>
              </Transition>
            </div>
          </template>
          <template v-else>
            <RouterLink to="/login" class="btn btn-outline" @click="menuOpen = false">
              Entrar
            </RouterLink>
            <RouterLink to="/register" class="btn btn-primary" @click="menuOpen = false">
              Cadastrar
            </RouterLink>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<style scoped>
.navbar {
  position: sticky;
  top: 0;
  z-index: 100;
  background: var(--bg-surface);
  border-bottom: 1px solid var(--border);
  backdrop-filter: blur(12px);
}

.navbar-inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 24px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.navbar-brand {
  display: flex;
  align-items: center;
  text-decoration: none;
}

.navbar-logo {
  height: 28px;
  width: auto;
}

.navbar-links {
  display: flex;
  align-items: center;
  gap: 8px;
}

.nav-link {
  padding: 6px 16px;
  border-radius: var(--radius-md);
  color: var(--text-secondary);
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  transition: color 0.15s ease, background-color 0.15s ease;
}

.nav-link:hover {
  color: var(--text-primary);
  background: var(--bg-emphasis);
  text-decoration: none;
}

.nav-link.active {
  color: var(--text-primary);
}

.navbar-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-left: 16px;
  padding-left: 16px;
  border-left: 1px solid var(--border-muted);
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 5px 16px;
  font-size: 14px;
  font-weight: 500;
  border-radius: var(--radius-md);
  border: 1px solid transparent;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.15s ease, border-color 0.15s ease;
  line-height: 20px;
}

.btn-outline {
  color: var(--text-primary);
  background: transparent;
  border-color: var(--border);
}

.btn-outline:hover {
  background: var(--bg-emphasis);
  border-color: var(--border-emphasis);
  text-decoration: none;
}

.btn-primary {
  color: var(--text-on-emphasis);
  background: var(--accent-green-dark);
  border-color: rgba(63, 185, 80, 0.4);
}

.btn-primary:hover {
  background: #2ea043;
  text-decoration: none;
}

.btn-primary:active {
  background: #238636;
}

/* User menu */
.user-menu {
  position: relative;
}

.user-trigger {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 8px;
  background: none;
  border: 1px solid transparent;
  border-radius: var(--radius-md);
  cursor: pointer;
  transition: background 0.15s ease, border-color 0.15s ease;
  font-family: var(--font-body);
}

.user-trigger:hover {
  background: var(--bg-emphasis);
  border-color: var(--border);
}

.user-avatar {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--accent-green-dark), var(--accent-green));
  color: var(--text-on-emphasis);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  letter-spacing: 0.02em;
  flex-shrink: 0;
}

.user-name {
  font-size: 14px;
  font-weight: 500;
  color: var(--text-primary);
}

.chevron {
  color: var(--text-subtle);
  transition: transform 0.2s ease;
}

.chevron.open {
  transform: rotate(180deg);
}

/* Dropdown */
.dropdown-menu {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  min-width: 200px;
  background: var(--bg-overlay);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-float);
  padding: 4px;
  z-index: 200;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
  padding: 8px 12px;
  font-size: 13px;
  color: var(--text-primary);
  background: none;
  border: none;
  border-radius: var(--radius-sm);
  cursor: pointer;
  text-decoration: none;
  font-family: var(--font-body);
  transition: background 0.1s ease;
}

.dropdown-item:hover {
  background: var(--bg-emphasis);
  text-decoration: none;
}

.dropdown-item-danger {
  color: var(--accent-red);
}

.dropdown-item-danger:hover {
  background: var(--accent-red-muted);
}

.dropdown-divider {
  height: 1px;
  background: var(--border-muted);
  margin: 4px 0;
}

/* Dropdown transitions */
.dropdown-enter-active {
  transition: opacity 0.15s ease, transform 0.15s ease;
}

.dropdown-leave-active {
  transition: opacity 0.1s ease, transform 0.1s ease;
}

.dropdown-enter-from {
  opacity: 0;
  transform: translateY(-4px);
}

.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

/* Mobile */
.menu-toggle {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
}

.menu-toggle span {
  display: block;
  width: 20px;
  height: 2px;
  background: var(--text-secondary);
  border-radius: 1px;
  transition: transform 0.2s ease, opacity 0.2s ease;
}

.menu-toggle.active span:nth-child(1) {
  transform: rotate(45deg) translate(5px, 5px);
}

.menu-toggle.active span:nth-child(2) {
  opacity: 0;
}

.menu-toggle.active span:nth-child(3) {
  transform: rotate(-45deg) translate(5px, -5px);
}

@media (max-width: 768px) {
  .menu-toggle {
    display: flex;
  }

  .navbar-links {
    position: fixed;
    top: 64px;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--bg-surface);
    flex-direction: column;
    align-items: stretch;
    padding: 16px 24px;
    gap: 4px;
    transform: translateX(100%);
    transition: transform 0.25s var(--ease-out);
  }

  .navbar-links.open {
    transform: translateX(0);
  }

  .nav-link {
    padding: 12px 16px;
    font-size: 16px;
  }

  .navbar-actions {
    flex-direction: column;
    align-items: stretch;
    margin-left: 0;
    padding-left: 0;
    padding-top: 16px;
    margin-top: 16px;
    border-left: none;
    border-top: 1px solid var(--border-muted);
  }

  .user-trigger {
    width: 100%;
    justify-content: flex-start;
    padding: 12px 16px;
  }

  .dropdown-menu {
    position: static;
    box-shadow: none;
    border: none;
    background: var(--bg-surface);
    padding: 0 0 0 16px;
  }
}
</style>
