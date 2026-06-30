<template>
  <header class="border-b border-border bg-card px-4 py-3 sm:px-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <div class="min-w-0">
        <h1 class="truncate text-xl font-semibold text-card-foreground">
          {{ title }}
        </h1>

        <p
          v-if="subtitle"
          class="mt-0.5 text-sm text-muted-foreground"
        >
          {{ subtitle }}
        </p>
      </div>

      <div class="flex w-full items-center gap-3 sm:w-auto">
        <div
          v-if="showSearch"
          class="relative min-w-0 flex-1 sm:w-64 sm:flex-none"
        >
          <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />

          <input
            :value="search"
            type="text"
            placeholder="Suchen..."
            class="h-10 w-full rounded-md border border-border bg-input pl-9 pr-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            @input="emit('update:search', $event.target.value)"
          />
        </div>

        <AppNotifications class="shrink-0" />
      </div>
    </div>
  </header>
</template>

<script setup>
import { Search } from 'lucide-vue-next'
import AppNotifications from '@/components/AppNotifications.vue'

defineProps({
  title: {
    type: String,
    required: true,
  },
  subtitle: {
    type: String,
    default: '',
  },
  showSearch: {
    type: Boolean,
    default: true,
  },
  search: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:search'])
</script>