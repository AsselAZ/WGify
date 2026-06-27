<template>
  <RouterView />
  <AppToastContainer />
</template>

<script setup>
import { RouterView } from 'vue-router'
import AppToastContainer from '@/components/AppToastContainer.vue'
</script>