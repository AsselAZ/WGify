<template>
  <div class="flex min-h-screen items-center justify-center bg-background p-4">
    <div class="grid w-full max-w-5xl grid-cols-1 items-center gap-8 md:grid-cols-2">
      <!-- Linke Info-Spalte -->
      <div class="hidden space-y-6 md:block">
        <div>
          <h1 class="mb-2 text-3xl font-bold">
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
            <CheckCircle class="mt-0.5 h-5 w-5 shrink-0 text-accent" />

            <span class="text-muted-foreground">
              {{ item }}
            </span>
          </li>
        </ul>
      </div>

      <!-- Formular -->
      <div class="w-full">
        <div class="mb-6 text-center md:hidden">
          <RouterLink to="/">
            <span class="text-3xl font-black">
              <span class="text-primary">WG</span><span class="text-purple">ify</span>
            </span>
          </RouterLink>
        </div>

        <div class="rounded-xl border border-border bg-card p-4 shadow-sm sm:p-6 md:p-8">
          <div class="mb-6 text-center">
            <div class="mb-4 hidden md:block">
              <WGLogo
                :width="100"
                :height="100"
                margin="auto"
                display="block"
              />
            </div>

            <h2 class="text-2xl font-bold">
              Konto erstellen
            </h2>

            <p class="mt-1 text-sm text-muted-foreground sm:text-base">
              Wähle, ob du eine neue WG erstellst oder einer WG beitrittst
            </p>
          </div>

          <form
            class="space-y-4"
            @submit.prevent="handleSubmit"
          >
            <div class="grid grid-cols-1 gap-2 rounded-lg bg-muted p-1 sm:grid-cols-2">
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

            <div
              v-if="form.mode === 'create'"
              class="space-y-2"
            >
              <label class="text-sm font-medium">WG-Name</label>

              <input
                v-model="form.apartmentName"
                type="text"
                placeholder="z.B. WG Musterstraße"
                required
                class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <div
              v-else
              class="space-y-2"
            >
              <label class="text-sm font-medium">Einladungscode</label>

              <input
                v-model="form.inviteCode"
                type="text"
                placeholder="z.B. ABC123"
                required
                class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm uppercase outline-none focus:ring-2 focus:ring-ring"
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
                class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">E-Mail</label>

              <input
                v-model="form.email"
                type="email"
                placeholder="deine@email.de"
                required
                class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div class="space-y-2">
                <label class="text-sm font-medium">Passwort</label>

                <input
                  v-model="form.password"
                  type="password"
                  placeholder="Mind. 8 Zeichen"
                  required
                  minlength="8"
                  class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
                />
              </div>

              <div class="space-y-2">
                <label class="text-sm font-medium">Passwort bestätigen</label>

                <input
                  v-model="form.confirm"
                  type="password"
                  placeholder="Wiederholen"
                  required
                  minlength="8"
                  class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
                />
              </div>
            </div>

            <button
              type="submit"
              :disabled="isLoading"
              class="h-10 w-full rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-60"
            >
              {{ isLoading ? 'Wird erstellt...' : submitLabel }}
            </button>
          </form>

          <p class="mt-6 text-center text-sm text-muted-foreground">
            Bereits ein Konto?

            <RouterLink
              to="/login"
              class="ml-1 font-medium text-purple hover:underline"
            >
              Jetzt anmelden
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
  </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue'
import {
  RouterLink,
  useRouter,
} from 'vue-router'
import { CheckCircle } from 'lucide-vue-next'
import WGLogo from '@/components/WGifyLogo.vue'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'
import { useMembersStore } from '@/stores/members'
import { useNotificationsStore } from '@/stores/notifications'

const membersStore = useMembersStore()
const notifications = useNotificationsStore()
const router = useRouter()
const authStore = useAuthStore()
const toast = useToastStore()

const isLoading = ref(false)

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
    const result = await authStore.register({
      name: form.name.trim(),
      email: form.email.trim(),
      password: form.password,
      passwordConfirmation: form.confirm,
      mode: form.mode,
      apartmentName: form.apartmentName.trim(),
      inviteCode: form.inviteCode.trim(),
    })

    if (result.requiresEmailVerification) {
      toast.success(
        'Registrierung erfolgreich',
        'Bitte bestätige jetzt deine E-Mail-Adresse.'
      )

      router.push({
        path: '/email-bestaetigen',
        query: {
          email: result.email,
        },
      })

      return
    }

    if (form.mode === 'join') {
      await membersStore.loadMembers()

      const joinedUserName = authStore.currentUser?.name || 'Ein neues Mitglied'
      const joinedUserEmail = authStore.currentUser?.email

      membersStore.members
        .filter((member) => member.email && member.email !== joinedUserEmail)
        .forEach((member) => {
          notifications.addNotificationForUser(
            member.email,
            'member-joined',
            'Neues Mitglied',
            `${joinedUserName} ist der WG beigetreten.`
          )
        })
    }

    toast.success('Registrierung erfolgreich', 'Dein Konto wurde erstellt.')
    router.push('/app/dashboard')
  } catch (error) {
    console.log('Register error:', error)
    console.log('Register response:', error.response?.data)

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
      toast.error(
        'Registrierung fehlgeschlagen',
        error.response?.data?.message ||
          error.message ||
          'Ein unerwarteter Fehler ist aufgetreten.'
      )
    }
  } finally {
    isLoading.value = false
  }
}
</script>