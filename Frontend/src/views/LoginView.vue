<template>
  <div class="flex min-h-screen items-center justify-center bg-background p-4">
    <div class="w-full max-w-md">
      <div class="rounded-xl border border-border bg-card p-4 shadow-sm sm:p-8">
        <div class="mb-6 text-center">
          <RouterLink to="/">
            <WGLogo
              :width="100"
              :height="100"
              margin="auto auto 25px auto"
              display="block"
            />
          </RouterLink>

          <h1 class="text-2xl font-bold">
            Willkommen zurück
          </h1>

          <p class="mt-1 text-sm text-muted-foreground">
            Melde dich an, um deine WG zu verwalten
          </p>
        </div>

        <form
          class="space-y-4"
          @submit.prevent="handleSubmit"
        >
          <div class="space-y-2">
            <label class="text-sm font-medium">E-Mail</label>

            <input
              v-model="email"
              type="email"
              placeholder="deine@email.de"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            />
          </div>

          <div class="space-y-2">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
              <label class="text-sm font-medium">Passwort</label>

              <RouterLink
                to="/passwort-vergessen"
                class="text-sm text-purple hover:underline"
              >
                Passwort vergessen?
              </RouterLink>
            </div>

            <input
              v-model="password"
              type="password"
              placeholder="Dein Passwort"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            />
          </div>

          <button
            type="submit"
            :disabled="isLoading"
            class="h-10 w-full rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-60"
          >
            {{ isLoading ? 'Wird angemeldet...' : 'Anmelden' }}
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-muted-foreground">
          Noch kein Konto?

          <RouterLink
            to="/registrieren"
            class="ml-1 font-medium text-purple hover:underline"
          >
            Jetzt registrieren
          </RouterLink>
        </p>
      </div>

      <p class="mt-6 text-center text-sm text-muted-foreground">
        <RouterLink
          to="/"
          class="hover:underline"
        >
          Zurück zur Startseite
        </RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import {
  RouterLink,
  useRouter,
} from 'vue-router'
import WGLogo from '@/components/WGifyLogo.vue'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const router = useRouter()
const authStore = useAuthStore()
const toast = useToastStore()

const email = ref('')
const password = ref('')
const isLoading = ref(false)

async function handleSubmit() {
  if (!email.value.trim()) {
    toast.error('E-Mail fehlt', 'Bitte gib deine E-Mail-Adresse ein.')
    return
  }

  if (!password.value) {
    toast.error('Passwort fehlt', 'Bitte gib dein Passwort ein.')
    return
  }

  isLoading.value = true

  try {
    const user = await authStore.login(
      email.value.trim(),
      password.value
    )

    toast.success('Login erfolgreich', 'Willkommen zurück.')

    if (user.apartment_id) {
      router.push('/app/dashboard')
    } else {
      router.push('/wg-auswahl')
    }
  } catch (error) {
    if (error.response?.data?.requiresEmailVerification) {
      toast.error(
        'E-Mail nicht bestätigt',
        error.response.data.message || 'Bitte bestätige zuerst deine E-Mail-Adresse.'
      )

      router.push({
        path: '/email-bestaetigen',
        query: {
          email: error.response.data.email || email.value.trim(),
        },
      })

      return
    }

    if (error.response?.status === 422) {
      toast.error('Login fehlgeschlagen', 'E-Mail oder Passwort ist falsch.')
    } else {
      toast.error(
        'Server nicht erreichbar',
        error.response?.data?.message || 'Bitte prüfe, ob das Backend läuft.'
      )
    }
  } finally {
    isLoading.value = false
  }
}
</script>