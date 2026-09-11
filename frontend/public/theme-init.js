;(function () {
  try {
    var stored = localStorage.getItem('uncrypt-theme')
    var theme =
      stored === 'dark' || stored === 'light'
        ? stored
        : window.matchMedia('(prefers-color-scheme: light)').matches
          ? 'light'
          : 'dark'
    document.documentElement.dataset.theme = theme
  } catch {}
})()