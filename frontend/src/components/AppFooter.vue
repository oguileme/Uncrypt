<template>
  <footer class="footer">
    <div class="footer-inner">
      <div class="footer-left">
        <img src="@/assets/uncrypt fonte sem fundo.png" alt="Uncrypt" class="footer-logo" />
        <span class="footer-copy">&copy; 2026 Uncrypt. Todos os direitos reservados.</span>
      </div>
      <div class="footer-links">
        <a href="https://github.com" target="_blank" rel="noopener">GitHub</a>
        <a href="#">Termos</a>
        <a href="#">Privacidade</a>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer {
  margin-top: auto;
  border-top: 1px solid var(--border-muted);
  background: var(--bg-surface);
}

.footer-inner {
  max-width: 1280px;
  margin: 0 auto;
  padding: 24px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.footer-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.footer-logo {
  height: 20px;
  width: auto;
  opacity: 0.7;
}

.footer-copy {
  color: var(--text-subtle);
  font-size: 12px;
}

.footer-links {
  display: flex;
  gap: 24px;
}

.footer-links a {
  color: var(--text-subtle);
  font-size: 12px;
  transition: color 0.15s ease;
}

.footer-links a:hover {
  color: var(--accent-blue);
}

@media (max-width: 768px) {
  .footer-inner {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }

  .footer-left {
    flex-direction: column;
    gap: 8px;
  }
}
</style>
