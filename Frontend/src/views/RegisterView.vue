<template>
  <div class="min-h-screen bg-background flex items-center justify-center p-4">
    <div class="w-full max-w-4xl grid md:grid-cols-2 gap-8 items-center">
      <div class="hidden md:block space-y-6">
        <div>
          <h1 class="text-3xl font-bold mb-2">
            Starte mit <span class="text-purple">WGify</span>
          </h1>
          <p class="text-muted-foreground">
            Erstelle eine neue WG oder tritt einer bestehenden WG per Einladungscode bei.
          </p>
        </div>

        <ul class="space-y-4">
          <li
            v-for="item in benefits"
            :key="item"
            class="flex items-start gap-3"
          >
            <CheckCircle class="h-5 w-5 text-accent flex-shrink-0 mt-0.5" />
            <span class="text-muted-foreground">{{ item }}</span>
          </li>
        </ul>
      </div>

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
              <WGLogo :width="100" :height="100" margin="auto" display="block" />
            </div>

            <h2 class="text-2xl font-bold">Konto erstellen</h2>
            <p class="text-muted-foreground mt-1">
              Wähle, ob du eine neue WG erstellst oder einer WG beitrittst
            </p>
          </div>

          <form class="space-y-4" @submit.prevent="handleSubmit">
            <div class="grid grid-cols-2 gap-2 rounded-lg bg-muted p-1">
              <button
                type="button"
                class="rounded-md px-3 py-2 text-sm font-medium transition-colors"
                :class="form.mode === 'create'
                  ? 'bg-card text-foreground shadow-sm'
                  : 'text-muted-foreground hover:text-foreground'"
                @click="form.mode = 'create'"
              >
                Neue WG
              </button>

              <button
                type="button"
                class="rounded-md px-3 py-2 text-sm font-medium transition-colors"
                :class="form.mode === 'join'
                  ? 'bg-card text-foreground shadow-sm'
                  : 'text-muted-foreground hover:text-foreground'"
                @click="form.mode = 'join'"
              >
                WG beitreten
              </button>
            </div>

            <div v-if="form.mode === 'create'" class="space-y-2">
              <label class="text-sm font-medium">WG-Name</label>
              <input
                v-model="form.apartmentName"
                type="text"
                placeholder="z.B. WG Musterstraße"
                required
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <div v-else class="space-y-2">
              <label class="text-sm font-medium">Einladungscode</label>
              <input
                v-model="form.inviteCode"
                type="text"
                placeholder="z.B. ABC123"
                required
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm uppercase outline-none focus:ring-2 focus:ring-ring"
              />
              <p class="text-xs text-muted-foreground">
                Du bekommst den Code von einem Mitglied der WG.
              </p>
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">Name</label>
              <input
                v-model="form.name"
                type="text"
                placeholder="Dein Name"
                required
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">E-Mail</label>
              <input
                v-model="form.email"
                type="email"
                placeholder="deine@email.de"
                required
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">Passwort</label>
              <input
                v-model="form.password"
                type="password"
                placeholder="Mind. 8 Zeichen"
                required
                minlength="8"
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">Passwort bestätigen</label>
              <input
                v-model="form.confirm"
                type="password"
                placeholder="Passwort wiederholen"
                required
                minlength="8"
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <button
              type="submit"
              :disabled="isLoading"
              class="w-full px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors disabled:opacity-60"
            >
              {{ isLoading ? 'Wird erstellt...' : submitLabel }}
            </button>
          </form>

          <p class="mt-6 text-center text-sm text-muted-foreground">
            Bereits ein Konto?
            <RouterLink to="/login" class="text-purple hover:underline font-medium ml-1">
              Jetzt anmelden
            </RouterLink>
          </p>
        </div>

        <p class="text-center text-sm text-muted-foreground mt-6">
          <RouterLink to="/" class="hover:underline">
            Zurück zur Startseite
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, reactive } from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import { CheckCircle } from 'lucide-vue-next'
import WGLogo from '@/components/WGifyLogo.vue'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'

const router = useRouter()
const authStore = useAuthStore()

const isLoading = ref(false)
const toast = useToastStore()

const form = reactive({
  mode: 'create',
  apartmentName: '',
  inviteCode: '',
  name: '',
  email: '',
  password: '',
  confirm: '',
})

const benefits = [
  'Erstelle deine eigene WG und werde automatisch Admin',
  'Lade Mitbewohner über einen Einladungscode ein',
  'Ausgaben und Aufgaben bleiben nur in deiner WG sichtbar',
  'Jede E-Mail kann nur zu einer einzigen WG gehören',
]

const submitLabel = computed(() => {
  return form.mode === 'create' ? 'WG erstellen' : 'WG beitreten'
})

async function handleSubmit() {

  if (form.password !== form.confirm) {
    toast.error('Registrierung fehlgeschlagen', 'Die Passwörter stimmen nicht überein.')
    return
  }

  if (form.mode === 'create' && !form.apartmentName.trim()) {
    toast.error('WG-Name fehlt', 'Bitte gib einen WG-Namen ein.')
    return
  }

  if (form.mode === 'join' && !form.inviteCode.trim()) {
    toast.error('Einladungscode fehlt', 'Bitte gib einen Einladungscode ein.')
    return
  }

  isLoading.value = true

  try {
    await authStore.register({
      name: form.name,
      email: form.email,
      password: form.password,
      passwordConfirmation: form.confirm,
      mode: form.mode,
      apartmentName: form.apartmentName,
      inviteCode: form.inviteCode,
    })
    toast.success('Registrierung erfolgreich', 'Dein Konto wurde erstellt.')

    router.push('/app/dashboard') //Erfolgereiche Registrierung

  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors

      toast.error(
        'Registrierung fehlgeschlagen',
        errors?.email?.[0] ||
          errors?.password?.[0] ||
          errors?.apartmentName?.[0] ||
          errors?.inviteCode?.[0] ||
          error.response.data.message ||
          'Bitte prüfe deine Eingaben.'
      )
    } else {
      toast.error('Server nicht erreichbar', 'Bitte prüfe, ob das Backend läuft.')
    }
  } finally {
    isLoading.value = false
  }
}
</script>