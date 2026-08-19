<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink, useRoute } from 'vue-router'

const menuOpen = ref(false)
const route = useRoute()
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
        <RouterLink
          to="/challenge"
          class="nav-link"
          :class="{ active: route.path === '/challenge' }"
          @click="menuOpen = false"
        >
          Desafios
        </RouterLink>
        <a href="#" class="nav-link" @click="menuOpen = false">Ranking</a>

        <div class="navbar-actions">
          <RouterLink to="/login" class="btn btn-outline" @click="menuOpen = false">
            Entrar
          </RouterLink>
          <RouterLink to="/register" class="btn btn-primary" @click="menuOpen = false">
            Cadastrar
          </RouterLink>
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
  transition: background 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
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
    margin-left: 0;
    padding-left: 0;
    padding-top: 16px;
    margin-top: 16px;
    border-left: none;
    border-top: 1px solid var(--border-muted);
    gap: 12px;
  }

  .btn {
    width: 100%;
    padding: 10px 16px;
  }
}
</style>
