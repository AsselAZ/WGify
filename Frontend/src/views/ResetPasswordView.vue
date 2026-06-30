<template>
  <div class="min-h-screen bg-background flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-sm">
      <h1 class="mb-2 text-2xl font-bold">Neues Passwort setzen</h1>

      <p class="mb-6 text-sm text-muted-foreground">
        Gib dein neues Passwort ein. Es muss mindestens 8 Zeichen lang sein.
      </p>

      <form class="space-y-4" @submit.prevent="submit">
        <div class="space-y-2">
          <label class="text-sm font-medium">Neues Passwort</label>
          <input
            v-model="password"
            type="password"
            class="w-full rounded-md border border-border bg-input px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring"
          />
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Passwort wiederholen</label>
          <input
            v-model="passwordConfirmation"
            type="password"
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
          class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90"
        >
          Passwort speichern
        </button>

        <RouterLink to="/login" class="block text-center text-sm text-primary hover:underline">
          Zurück zum Login
        </RouterLink>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { api } from '@/lib/api'

const route = useRoute()

const password = ref('')
const passwordConfirmation = ref('')
const message = ref('')
const error = ref('')

async function submit() {
  message.value = ''
  error.value = ''

  if (password.value.length < 8) {
    error.value = 'Das Passwort muss mindestens 8 Zeichen lang sein.'
    return
  }

  if (password.value !== passwordConfirmation.value) {
    error.value = 'Die Passwörter stimmen nicht überein.'
    return
  }

  const response = await api.post('/password/reset', {
    email: route.query.email,
    token: route.query.token,
    password: password.value,
    password_confirmation: passwordConfirmation.value,
  })

  message.value = response.data.message
}
</script>