<template>
  <div class="min-h-screen bg-background flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-sm">
      <h1 class="mb-2 text-2xl font-bold">Passwort vergessen?</h1>

      <p class="mb-6 text-sm text-muted-foreground">
        Gib deine E-Mail-Adresse ein. Wenn sie existiert, erhältst du eine E-Mail zum Zurücksetzen.
      </p>

      <form class="space-y-4" @submit.prevent="submit">
        <div class="space-y-2">
          <label class="text-sm font-medium">E-Mail</label>
          <input
            v-model="email"
            type="email"
            class="w-full rounded-md border border-border bg-input px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring"
          />
        </div>

        <p v-if="message" class="rounded-md bg-green-50 px-3 py-2 text-sm text-green-700">
          {{ message }}
        </p>

        <p v-if="error" class="rounded-md bg-red-50 px-3 py-2 text-sm text-red-700">
          {{ error }}
        </p>

        <button
  type="submit"
  :disabled="isLoading"
  class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 disabled:opacity-60"
>
  {{ isLoading ? 'Wird gesendet...' : 'E-Mail senden' }}
</button>

        <RouterLink
          to="/login"
          class="block text-center text-sm text-primary hover:underline"
        >
          Zurück zum Login
        </RouterLink>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink } from 'vue-router'
import { api } from '@/lib/api'

const email = ref('')
const message = ref('')
const error = ref('')
const isLoading = ref(false)

async function submit() {
  if (isLoading.value) {
    return
  }

  message.value = ''
  error.value = ''

  if (!email.value.trim()) {
    error.value = 'Bitte gib deine E-Mail-Adresse ein.'
    return
  }

  isLoading.value = true

  try {
    const response = await api.post('/password/forgot', {
      email: email.value,
    })

    message.value = response.data.message
  } finally {
    isLoading.value = false
  }
}
</script>
