<template>
  <div class="fixed right-4 top-4 z-[9999] flex w-full max-w-sm flex-col-reverse gap-3">
    <div
      v-for="toast in sortedToasts"
      :key="toast.id"
      class="rounded-xl border border-border bg-card p-4 shadow-xl"
    >
      <div class="flex gap-3">
        <div
          class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
          :class="iconBoxClass(toast.type)"
        >
          <CheckCircle v-if="toast.type === 'success'" class="h-5 w-5" />
          <AlertCircle v-else-if="toast.type === 'error'" class="h-5 w-5" />
          <Info v-else class="h-5 w-5" />
        </div>

        <div class="min-w-0 flex-1">
          <p class="text-sm font-semibold">
            {{ toast.title }}
          </p>

          <p v-if="toast.message" class="mt-1 text-sm text-muted-foreground">
            {{ toast.message }}
          </p>
        </div>

        <button
          type="button"
          class="rounded-md p-1 text-muted-foreground hover:bg-muted hover:text-foreground transition-colors"
          @click="toastStore.removeToast(toast.id)"
        >
          <X class="h-4 w-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { AlertCircle, CheckCircle, Info, X } from 'lucide-vue-next'
import { useToastStore } from '@/stores/toast'

const toastStore = useToastStore()

const sortedToasts = computed(() => {
  return [...toastStore.toasts].sort((a, b) => b.createdAt - a.createdAt)
})

function iconBoxClass(type) {
  if (type === 'success') {
    return 'bg-green-100 text-green-700'
  }

  if (type === 'error') {
    return 'bg-red-100 text-red-700'
  }

  return 'bg-primary/10 text-primary'
}
</script>