<template>
  <section class="how-it-works">
    <div class="how-inner">
      <div class="section-header">
        <h2 class="section-title">Como Funciona</h2>
        <p class="section-subtitle">
          Tr&ecirc;s passos para come&ccedil;ar a desvendar c&oacute;digos
        </p>
      </div>

      <div class="steps">
        <div class="step">
          <div class="step-number">1</div>
          <div class="step-content">
            <h3 class="step-title">Escolha um Desafio</h3>
            <p class="step-desc">
              Navegue pela lista de cifras dispon&iacute;veis. Cada uma tem dificuldade e dicas pr&oacute;prias.
            </p>
          </div>
          <div class="step-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <circle cx="11" cy="11" r="8" />
              <path d="M21 21l-4.35-4.35" />
            </svg>
          </div>
        </div>

        <div class="step-connector"></div>

        <div class="step">
          <div class="step-number">2</div>
          <div class="step-content">
            <h3 class="step-title">Analise o C&oacute;digo</h3>
            <p class="step-desc">
              Leia a mensagem cifrada, pense na l&oacute;gica por tr&aacute;s da cifra e use as dicas se precisar.
            </p>
          </div>
          <div class="step-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z" />
              <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" />
            </svg>
          </div>
        </div>

        <div class="step-connector"></div>

        <div class="step">
          <div class="step-number">3</div>
          <div class="step-content">
            <h3 class="step-title">Decifre a Mensagem</h3>
            <p class="step-desc">
              Digite a mensagem descriptografada e veja se acertou. Ganhe pontos e suba de n&iacute;vel!
            </p>
          </div>
          <div class="step-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
              <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.how-it-works {
  padding: 80px 24px;
  border-top: 1px solid var(--border-muted);
}

.how-inner {
  max-width: 640px;
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

.steps {
  display: flex;
  flex-direction: column;
  gap: 0;
}

.step {
  display: flex;
  align-items: flex-start;
  gap: 20px;
  padding: 24px;
  background: var(--bg-surface);
  border: 1px solid var(--border);
  border-radius: var(--radius-md);
  position: relative;
}

.step-number {
  flex-shrink: 0;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--accent-green-dark), var(--accent-green));
  color: var(--text-on-emphasis);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  font-family: var(--font-mono);
}

.step-content {
  flex: 1;
}

.step-title {
  font-size: 16px;
  font-weight: 600;
  color: var(--text-primary);
  margin-bottom: 4px;
}

.step-desc {
  font-size: 14px;
  color: var(--text-secondary);
  line-height: 1.5;
}

.step-icon {
  flex-shrink: 0;
  color: var(--text-subtle);
  margin-top: 2px;
}

.step-connector {
  width: 2px;
  height: 16px;
  background: var(--border);
  margin: 0 auto;
  position: relative;
}

.step-connector::before,
.step-connector::after {
  content: '';
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: var(--border-emphasis);
}

.step-connector::before {
  top: -3px;
}

.step-connector::after {
  bottom: -3px;
}

@media (max-width: 768px) {
  .how-it-works {
    padding: 48px 16px;
  }

  .step {
    flex-wrap: wrap;
  }

  .step-icon {
    display: none;
  }
}
</style>
