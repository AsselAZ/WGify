<template>
  <div class="flex min-h-screen items-center justify-center bg-background p-4">
    <div class="w-full max-w-md rounded-xl border border-border bg-card p-4 shadow-sm sm:p-6">
      <div class="text-center">
        <h1 class="text-2xl font-bold">
          Neues Passwort setzen
        </h1>

        <p class="mt-2 text-sm text-muted-foreground">
          Gib dein neues Passwort ein. Es muss mindestens 8 Zeichen lang sein.
        </p>
      </div>

      <form
        class="mt-6 space-y-4"
        @submit.prevent="submit"
      >
        <div class="space-y-2">
          <label class="text-sm font-medium">Neues Passwort</label>

          <input
            v-model="password"
            type="password"
            class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            placeholder="Neues Passwort"
          />
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Passwort wiederholen</label>

          <input
            v-model="passwordConfirmation"
            type="password"
            class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            placeholder="Passwort wiederholen"
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
          {{ isLoading ? 'Wird gespeichert...' : 'Passwort speichern' }}
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
import {
  RouterLink,
  useRoute,
} from 'vue-router'
import { api } from '@/lib/api'

const route = useRoute()

const password = ref('')
const passwordConfirmation = ref('')
const message = ref('')
const error = ref('')
const isLoading = ref(false)

async function submit() {
  if (isLoading.value) {
    return
  }

  message.value = ''
  error.value = ''

  if (!route.query.email || !route.query.token) {
    error.value = 'Der Zurücksetzen-Link ist ungültig oder unvollständig.'
    return
  }

  if (password.value.length < 8) {
    error.value = 'Das Passwort muss mindestens 8 Zeichen lang sein.'
    return
  }

  if (password.value !== passwordConfirmation.value) {
    error.value = 'Die Passwörter stimmen nicht überein.'
    return
  }

  isLoading.value = true

  try {
    const response = await api.post('/password/reset', {
      email: route.query.email,
      token: route.query.token,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    })

    message.value =
      response.data.message ||
      'Dein Passwort wurde erfolgreich geändert.'
  } catch (err) {
    error.value =
      err.response?.data?.errors?.email?.[0] ||
      err.response?.data?.errors?.token?.[0] ||
      err.response?.data?.errors?.password?.[0] ||
      err.response?.data?.message ||
      'Das Passwort konnte nicht gespeichert werden. Bitte versuche es erneut.'
  } finally {
    isLoading.value = false
  }
}
</script>