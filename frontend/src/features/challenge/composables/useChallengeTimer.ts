import { ref, onScopeDispose } from 'vue'

export function useChallengeTimer() {
  const elapsed = ref(0)
  const running = ref(false)

  let startedAt = 0
  let accumulated = 0
  let interval: ReturnType<typeof setInterval> | null = null

  function tick() {
    elapsed.value = accumulated + Math.floor((Date.now() - startedAt) / 1000)
  }

  function start() {
    if (running.value || interval) return
    running.value = true
    startedAt = Date.now()
    interval = setInterval(tick, 500)
  }

  function stop() {
    if (!running.value) return
    tick()
    accumulated = elapsed.value
    running.value = false
    if (interval) {
      clearInterval(interval)
      interval = null
    }
  }

  function reset() {
    stop()
    accumulated = 0
    elapsed.value = 0
  }

  onScopeDispose(() => {
    if (interval) {
      clearInterval(interval)
    }
  })

  return { elapsed, running, start, stop, reset }
}