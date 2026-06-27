<template>
  <header class="flex items-center justify-between border-b border-border bg-card px-4 py-3 md:px-6">
    <div>
      <h1 class="text-xl font-semibold text-card-foreground">{{ title }}</h1>
      <p v-if="subtitle" class="text-sm text-muted-foreground">{{ subtitle }}</p>
    </div>

    <div class="flex items-center gap-3">
      <div v-if="showSearch" class="relative">
        <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
        <input
          :value="search"
          type="text"
          placeholder="Suchen..."
          class="h-9 w-48 rounded-md border border-border bg-input pl-9 pr-3 text-sm outline-none focus:ring-2 focus:ring-ring md:w-64"
          @input="emit('update:search', $event.target.value)"
        />
      </div>

      <AppNotifications />
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