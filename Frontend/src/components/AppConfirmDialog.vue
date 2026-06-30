<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-3 sm:items-center sm:p-4"
    @click.self="close"
  >
    <div class="w-full max-w-md rounded-t-2xl border border-border bg-card p-4 shadow-lg sm:rounded-xl sm:p-6">
      <div class="flex items-start gap-3 sm:gap-4">
        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600 sm:h-11 sm:w-11">
          <AlertTriangle class="h-5 w-5" />
        </div>

        <div class="min-w-0 flex-1">
          <h3 class="text-lg font-semibold">
            {{ title }}
          </h3>

          <p class="mt-1 text-sm text-muted-foreground">
            {{ message }}
          </p>
        </div>
      </div>

      <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
        <button
          type="button"
          class="h-10 w-full rounded-md border border-border px-4 text-sm font-medium transition-colors hover:bg-muted sm:w-auto"
          @click="close"
        >
          {{ cancelText }}
        </button>

        <button
          type="button"
          class="h-10 w-full rounded-md bg-red-600 px-4 text-sm font-medium text-white transition-colors hover:bg-red-700 sm:w-auto"
          @click="confirm"
        >
          {{ confirmText }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { AlertTriangle } from 'lucide-vue-next'

defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  message: {
    type: String,
    required: true,
  },
  confirmText: {
    type: String,
    default: 'Bestätigen',
  },
  cancelText: {
    type: String,
    default: 'Abbrechen',
  },
})

const emit = defineEmits([
  'update:modelValue',
  'confirm',
])

function close() {
  emit('update:modelValue', false)
}

function confirm() {
  emit('confirm')
  emit('update:modelValue', false)
}
</script>