<template>
  <div class="min-h-screen bg-background">
    <AppSidebar />
    <main class="md:pl-64">
      <RouterView />
    </main>
  </div>
</template>

<script setup lang="ts">
import { RouterView } from 'vue-router'
import AppSidebar from '@/components/AppSidebar.vue'
</script>
