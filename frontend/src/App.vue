<script setup lang="ts">
import { RouterView } from 'vue-router'
import AppNavbar from './components/AppNavbar.vue'
import AppFooter from './components/AppFooter.vue'
import FeedbackWidget from './features/feedback/components/FeedbackWidget.vue'
</script>

<template>
  <AppNavbar />
  <RouterView />
  <AppFooter />
  <FeedbackWidget />
</template>

<style>
@import './assets/main.css';
</style>
