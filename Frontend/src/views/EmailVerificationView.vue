<template>
  <div class="flex min-h-screen items-center justify-center bg-background p-4">
    <div class="w-full max-w-md rounded-xl border border-border bg-card p-4 shadow-sm sm:p-6">
      <div class="text-center">
        <h1 class="text-2xl font-bold">
          E-Mail bestätigen
        </h1>

        <p class="mt-2 text-sm text-muted-foreground">
          Wir haben dir einen 6-stelligen Code gesendet. Der Code ist 5 Minuten gültig.
        </p>
      </div>

      <div class="mt-6 space-y-4">
        <div class="space-y-2">
          <label class="text-sm font-medium">E-Mail-Adresse</label>

          <input
            v-model="email"
            type="email"
            class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            placeholder="deine@email.de"
          />
        </div>

        <div class="space-y-2">
          <label class="text-sm font-medium">Bestätigungscode</label>

          <input
            v-model="code"
            type="text"
            maxlength="6"
            inputmode="numeric"
            class="h-10 w-full rounded-md border border-border bg-input px-3 text-center text-sm tracking-[0.35em] outline-none focus:ring-2 focus:ring-ring"
            placeholder="123456"
          />
        </div>

        <button
          type="button"
          class="h-10 w-full rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-50"
          :disabled="isLoading"
          @click="verifyEmail"
        >
          {{ isLoading ? 'Wird geprüft...' : 'E-Mail bestätigen' }}
        </button>

        <button
          type="button"
          class="h-10 w-full rounded-md border border-border px-4 text-sm font-medium transition-colors hover:bg-muted disabled:opacity-50"
          :disabled="isResending"
          @click="resendCode"
        >
          {{ isResending ? 'Wird gesendet...' : 'Code erneut senden' }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const toast = useToastStore()

const email = ref(route.query.email || '')
const code = ref('')
const isLoading = ref(false)
const isResending = ref(false)

async function verifyEmail() {
  if (!email.value) {
    toast.error('E-Mail fehlt', 'Bitte gib deine E-Mail-Adresse ein.')
    return
  }

  if (!code.value || code.value.length !== 6) {
    toast.error('Code ungültig', 'Bitte gib den 6-stelligen Code ein.')
    return
  }

  isLoading.value = true

  try {
    await authStore.verifyEmail(email.value, code.value)

    toast.success(
      'E-Mail bestätigt',
      'Deine E-Mail-Adresse wurde erfolgreich bestätigt.'
    )

    router.push('/app/dashboard')
  } catch (error) {
    toast.error(
      'Bestätigung fehlgeschlagen',
      error.response?.data?.errors?.code?.[0] ||
        error.response?.data?.errors?.email?.[0] ||
        error.response?.data?.message ||
        'Bitte versuche es erneut.'
    )
  } finally {
    isLoading.value = false
  }
}

async function resendCode() {
  if (!email.value) {
    toast.error('E-Mail fehlt', 'Bitte gib deine E-Mail-Adresse ein.')
    return
  }

  isResending.value = true

  try {
    await authStore.resendEmailVerification(email.value)

    toast.success(
      'Code gesendet',
      'Falls die E-Mail registriert ist, wurde ein neuer Code gesendet.'
    )
  } catch (error) {
    toast.error(
      'Code konnte nicht gesendet werden',
      error.response?.data?.message || 'Bitte versuche es erneut.'
    )
  } finally {
    isResending.value = false
  }
}
</script>