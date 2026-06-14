<template>
  <div class="min-h-screen bg-background flex items-center justify-center p-4">
    <div class="w-full max-w-4xl grid md:grid-cols-2 gap-8 items-center">
      <!-- Benefits -->
      <div class="hidden md:block space-y-6">
        <div>
          <h1 class="text-3xl font-bold mb-2">
            Starte mit <span class="text-purple">WGify</span>
          </h1>
          <p class="text-muted-foreground">Registriere dich kostenlos und organisiere deine WG noch heute.</p>
        </div>
        <ul class="space-y-4">
          <li v-for="item in benefits" :key="item" class="flex items-start gap-3">
            <CheckCircle class="h-5 w-5 text-accent flex-shrink-0 mt-0.5" />
            <span class="text-muted-foreground">{{ item }}</span>
          </li>
        </ul>
      </div>

      <!-- Form -->
      <div>
        <div class="text-center mb-6 md:hidden">
          <RouterLink to="/">
            <span class="text-3xl font-black">
              <span class="text-primary">WG</span><span class="text-purple">ify</span>
            </span>
          </RouterLink>
        </div>

        <div class="rounded-xl border border-border bg-card p-8 shadow-sm">
          <div class="text-center mb-6">
            <div class="hidden md:block mb-4">
              <span class="text-2xl font-black">
                <span class="text-primary">WG</span><span class="text-purple">ify</span>
              </span>
            </div>
            <h2 class="text-2xl font-bold">Konto erstellen</h2>
            <p class="text-muted-foreground mt-1">Erstelle ein kostenloses Konto</p>
          </div>

          <form class="space-y-4" @submit.prevent="handleSubmit">
            <div class="space-y-2">
              <label class="text-sm font-medium">Name</label>
              <input v-model="form.name" type="text" placeholder="Dein Name" required
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium">E-Mail</label>
              <input v-model="form.email" type="email" placeholder="deine@email.de" required
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium">Passwort</label>
              <input v-model="form.password" type="password" placeholder="Mind. 8 Zeichen" required minlength="8"
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium">Passwort bestätigen</label>
              <input v-model="form.confirm" type="password" placeholder="Passwort wiederholen" required minlength="8"
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
            </div>
            <button type="submit" :disabled="isLoading"
              class="w-full px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors disabled:opacity-60">
              {{ isLoading ? 'Wird erstellt...' : 'Konto erstellen' }}
            </button>
          </form>

          <p class="mt-6 text-center text-sm text-muted-foreground">
            Bereits ein Konto?
            <RouterLink to="/login" class="text-purple hover:underline font-medium ml-1">Jetzt anmelden</RouterLink>
          </p>
        </div>

        <p class="text-center text-sm text-muted-foreground mt-6">
          <RouterLink to="/" class="hover:underline">Zurück zur Startseite</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { CheckCircle } from 'lucide-vue-next'

const router = useRouter()
const isLoading = ref(false)
const form = reactive({ name: '', email: '', password: '', confirm: '' })

const benefits = [
  'Ausgaben fair aufteilen und immer den Überblick behalten',
  'Aufgaben planen und Putzpläne erstellen',
  'Mitbewohner einladen und zusammenarbeiten',
  'Kostenlos und auf allen Geräten nutzbar',
]

async function handleSubmit() {
  isLoading.value = true
  await new Promise(resolve => setTimeout(resolve, 1000))
  router.push('/app/dashboard')
}
</script>
