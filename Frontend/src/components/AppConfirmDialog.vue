<template>
  <div
    v-if="modelValue"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    @click.self="close"
  >
    <div class="w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-lg">
      <div class="flex items-start gap-4">
        <div
          class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600"
        >
          <AlertTriangle class="h-5 w-5" />
        </div>

        <div class="flex-1">
          <h3 class="text-lg font-semibold">
            {{ title }}
          </h3>

          <p class="mt-1 text-sm text-muted-foreground">
            {{ message }}
          </p>
        </div>
      </div>

      <div class="mt-6 flex justify-end gap-3">
        <button
          type="button"
          class="rounded-md border border-border px-4 py-2 text-sm font-medium hover:bg-muted transition-colors"
          @click="close"
        >
          {{ cancelText }}
        </button>

        <button
          type="button"
          class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors"
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

const emit = defineEmits(['update:modelValue', 'confirm'])

function close() {
  emit('update:modelValue', false)
}

function confirm() {
  emit('confirm')
  emit('update:modelValue', false)
}
</script>