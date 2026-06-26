<template>
  <div class="min-h-screen bg-background flex items-center justify-center p-4">
    <div class="w-full max-w-md">
      <!--
      <div class="text-center mb-8">
        <RouterLink to="/">
          <span class="text-3xl font-black">
            <span class="text-primary">WG</span><span class="text-purple">ify</span>
          </span>
        </RouterLink>
      </div>
       -->

      <div class="rounded-xl border border-border bg-card p-8 shadow-sm">
        <div class="text-center mb-6">
          <RouterLink to="/">
          <!-- Logo -->
          <WGLogo :width="100" :height="100" margin="auto auto 25px auto" display="block"/>
          </RouterLink>
          <h1 class="text-2xl font-bold">Willkommen zurück</h1>
          <p class="text-muted-foreground mt-1">Melde dich an, um deine WG zu verwalten</p>
        </div>

        <form class="space-y-4" @submit.prevent="handleSubmit">
          <div class="space-y-2">
            <label class="text-sm font-medium">E-Mail</label>
            <input v-model="email" type="email" placeholder="deine@email.de" required
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
          </div>
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <label class="text-sm font-medium">Passwort</label>
              <a href="#" class="text-sm text-purple hover:underline">Passwort vergessen?</a>
            </div>
            <input v-model="password" type="password" placeholder="Dein Passwort" required
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
          </div>
	  <p v-if="errorMessage" class="text-sm text-red-500">
  		{{ errorMessage }}
	  </p>
          <button type="submit" :disabled="isLoading"
            class="w-full px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors disabled:opacity-60">
            {{ isLoading ? 'Wird angemeldet...' : 'Anmelden' }}
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-muted-foreground">
          Noch kein Konto?
          <RouterLink to="/registrieren" class="text-purple hover:underline font-medium ml-1">Jetzt registrieren</RouterLink>
        </p>
      </div>

      <p class="text-center text-sm text-muted-foreground mt-6">
        <RouterLink to="/" class="hover:underline">Zurück zur Startseite</RouterLink>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import WGLogo from '@/components/WGifyLogo.vue'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const isLoading = ref(false)
const errorMessage = ref('')

async function handleSubmit() {
  isLoading.value = true
  errorMessage.value = ''

  try {
    const user = await authStore.login(email.value, password.value)

    if (user.apartment_id) {
      router.push('/app/dashboard')
    } else {
      router.push('/wg-auswahl')
    }
  } catch (error) {
    if (error.response?.status === 422) {
      errorMessage.value = 'E-Mail oder Passwort ist falsch.'
    } else {
      errorMessage.value = 'Server nicht erreichbar.'
    }
  } finally {
    isLoading.value = false
  }
}
</script>