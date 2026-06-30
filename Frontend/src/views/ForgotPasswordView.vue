<template>
  <div class="flex min-h-screen items-center justify-center bg-background p-4">
    <div class="w-full max-w-md rounded-xl border border-border bg-card p-4 shadow-sm sm:p-6">
      <div class="text-center">
        <h1 class="text-2xl font-bold">
          Passwort vergessen?
        </h1>

        <p class="mt-2 text-sm text-muted-foreground">
          Gib deine E-Mail-Adresse ein. Wenn sie existiert, erhältst du eine E-Mail zum Zurücksetzen.
        </p>
      </div>

      <form
        class="mt-6 space-y-4"
        @submit.prevent="submit"
      >
        <div class="space-y-2">
          <label class="text-sm font-medium">E-Mail</label>

          <input
            v-model="email"
            type="email"
            class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            placeholder="deine@email.de"
          />
        </div>

        <p
          v-if="message"
          class="rounded-md bg-green-50 px-3 py-2 text-sm text-green-700"
        >
          {{ message }}
        </p>

        <p
          v-if="error"
          class="rounded-md bg-red-50 px-3 py-2 text-sm text-red-700"
        >
          {{ error }}
        </p>

        <button
          type="submit"
          :disabled="isLoading"
          class="h-10 w-full rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-60"
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
      email: email.value.trim(),
    })

    message.value =
      response.data.message ||
      'Falls diese E-Mail registriert ist, wurde eine E-Mail zum Zurücksetzen gesendet.'
  } catch (err) {
    error.value =
      err.response?.data?.errors?.email?.[0] ||
      err.response?.data?.message ||
      'Die E-Mail konnte nicht gesendet werden. Bitte versuche es erneut.'
  } finally {
    isLoading.value = false
  }
}
</script>