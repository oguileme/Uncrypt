<script setup lang="ts">
import HeroSection from './components/HeroSection.vue'
import FeaturesSection from './components/FeaturesSection.vue'
import HowItWorks from './components/HowItWorks.vue'
</script>

<template>
  <main class="landing">
    <HeroSection />
    <FeaturesSection />
    <HowItWorks />
  </main>
</template>

<style scoped>
.landing {
  flex: 1;
}
</style>
