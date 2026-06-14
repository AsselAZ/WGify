<template>
  <header class="sticky top-0 z-20 flex h-16 items-center justify-between border-b border-border bg-background/95 backdrop-blur px-4 md:px-6">
    <div class="ml-12 md:ml-0">
      <h1 class="text-lg font-semibold text-foreground">{{ title }}</h1>
      <p v-if="subtitle" class="text-sm text-muted-foreground">{{ subtitle }}</p>
    </div>

    <div class="flex items-center gap-2 md:gap-4">
      <div class="hidden md:flex relative">
        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
        <input
          placeholder="Suchen..."
          class="w-64 pl-9 pr-3 h-9 rounded-md bg-secondary border-0 text-sm outline-none focus:ring-2 focus:ring-ring"
        />
      </div>
      <button class="relative flex items-center justify-center w-9 h-9 rounded-md hover:bg-accent transition-colors">
        <Bell class="h-5 w-5" />
        <span class="absolute -top-0.5 -right-0.5 h-4 w-4 rounded-full bg-primary text-[10px] font-medium text-primary-foreground flex items-center justify-center">
          3
        </span>
      </button>
    </div>
  </header>
</template>

<script setup lang="ts">
import { Search, Bell } from 'lucide-vue-next'

defineProps<{
  title: string
  subtitle?: string
}>()
</script>
