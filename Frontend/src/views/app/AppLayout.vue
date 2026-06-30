<template>
  <div class="min-h-screen overflow-x-hidden bg-background">
    <AppSidebar />

    <main class="min-h-screen lg:pl-64">
      <div class="mx-auto w-full max-w-[1400px] px-4 pb-6 pt-20 sm:px-6 sm:pt-8 lg:px-8 lg:pt-8">
        <RouterView />
      </div>
    </main>
  </div>
</template>

<script setup>
import { RouterView } from 'vue-router'
import AppSidebar from '@/components/AppSidebar.vue'
</script>