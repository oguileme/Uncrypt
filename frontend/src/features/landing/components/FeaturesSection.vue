<template>
  <section class="features">
    <div class="features-inner">
      <div class="section-header">
        <h2 class="section-title">Por que Uncrypt?</h2>
        <p class="section-subtitle">
          Aprender criptografia nunca foi t&atilde;o divertido
        </p>
      </div>

      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon icon-green">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <rect x="3" y="11" width="18" height="11" rx="2" />
              <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              <circle cx="12" cy="16" r="1" fill="currentColor" />
            </svg>
          </div>
          <h3 class="feature-title">Cifras Reais</h3>
          <p class="feature-desc">
            Trabalhe com C&eacute;sar, Vigen&egrave;re, Morse, Base64 e mais. Cada desafio usa criptografia de verdade.
          </p>
        </div>

        <div class="feature-card">
          <div class="feature-icon icon-blue">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M12 2L2 7l10 5 10-5-10-5z" />
              <path d="M2 17l10 5 10-5" />
              <path d="M2 12l10 5 10-5" />
            </svg>
          </div>
          <h3 class="feature-title">Progress&atilde;o por N&iacute;vel</h3>
          <p class="feature-desc">
            Comece f&aacute;cil e v&aacute; subindo. Cada cifra desbloqueada te aproxima de se tornar um verdadeiro cript&oacute;grafo.
          </p>
        </div>

        <div class="feature-card">
          <div class="feature-icon icon-purple">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6" />
              <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18" />
              <path d="M4 22h16" />
              <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22" />
              <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22" />
              <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z" />
            </svg>
          </div>
          <h3 class="feature-title">Conquistas &amp; Rankings</h3>
          <p class="feature-desc">
            Desbloqueie conquistas, acumule pontos e suba no ranking. Desafie seus amigos e prove quem &eacute; o mestre dos c&oacute;digos.
          </p>
        </div>

        <div class="feature-card">
          <div class="feature-icon icon-yellow">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20z" />
              <path d="M12 6v6l4 2" />
            </svg>
          </div>
          <h3 class="feature-title">Desafios Di&aacute;rios</h3>
          <p class="feature-desc">
            Novos desafios todos os dias para manter suas habilidades afiadas. Streaks e b&ocirc;nus por consist&ecirc;ncia.
          </p>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.features {
  padding: 80px 24px;
  border-top: 1px solid var(--border-muted);
}

.features-inner {
  max-width: 1012px;
  margin: 0 auto;
}

.section-header {
  text-align: center;
  margin-bottom: 48px;
}

.section-title {
  font-size: 28px;
  font-weight: 700;
  color: var(--text-primary);
  margin-bottom: 8px;
}

.section-subtitle {
  font-size: 16px;
  color: var(--text-secondary);
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
}

.feature-card {
  padding: 24px;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
}

.feature-card:hover {
  border-color: var(--border-emphasis);
  transform: translateY(-2px);
  box-shadow: var(--shadow-float);
}

.feature-icon {
  width: 40px;
  height: 40px;
  border-radius: var(--radius-md);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
}

.icon-green {
  background: var(--accent-green-muted);
  color: var(--accent-green);
}

.icon-blue {
  background: var(--accent-blue-muted);
  color: var(--accent-blue);
}

.icon-purple {
  background: var(--accent-purple-muted);
  color: var(--accent-purple);
}

.icon-yellow {
  background: var(--accent-yellow-muted);
  color: var(--accent-yellow);
}

.feature-title {
  font-size: 16px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 8px;
}

.feature-desc {
  font-size: 14px;
  color: var(--text-secondary);
  line-height: 1.5;
}

@media (max-width: 768px) {
  .features {
    padding: 48px 16px;
  }

  .features-grid {
    grid-template-columns: 1fr;
  }
}
</style>
