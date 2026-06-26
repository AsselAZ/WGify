<template>
  <div class="min-h-screen">
    <AppNavbar
      title="Einstellungen"
      subtitle="Verwalte dein Profil und die WG"
      :show-search="false"
    />

    <div class="p-4 md:p-6 space-y-6 max-w-3xl">
      <!-- Profil -->
      <div class="rounded-xl border border-border bg-card p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary">
            <User class="h-5 w-5 text-primary" />
          </div>

          <div>
            <h2 class="text-lg font-semibold">Profil</h2>
            <p class="text-sm text-muted-foreground">
              Persönliche Informationen bearbeiten
            </p>
          </div>
        </div>

        <div class="space-y-4">
          <div class="flex items-center gap-4">
            <div
              class="flex h-16 w-16 items-center justify-center rounded-full bg-primary text-primary-foreground text-2xl font-semibold"
            >
              {{ profile.avatar }}
            </div>

            <button
              type="button"
              class="px-4 py-2 rounded-md border border-border text-sm font-medium hover:bg-muted transition-colors"
            >
              Bild ändern
            </button>
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
              <label class="text-sm font-medium">Name</label>
              <input
                v-model="profile.name"
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">E-Mail</label>
              <input
                v-model="profile.email"
                type="email"
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Passwort -->
      <div class="rounded-xl border border-border bg-card p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple/15">
            <Lock class="h-5 w-5 text-purple" />
          </div>

          <div>
            <h2 class="text-lg font-semibold">Passwort</h2>
            <p class="text-sm text-muted-foreground">Passwort ändern</p>
          </div>
        </div>

        <div class="space-y-4">
          <div class="space-y-2">
            <label class="text-sm font-medium">Aktuelles Passwort</label>
            <input
              type="password"
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
            />
          </div>

          <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
              <label class="text-sm font-medium">Neues Passwort</label>
              <input
                type="password"
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">Passwort bestätigen</label>
              <input
                type="password"
                class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>
          </div>

          <button
            type="button"
            class="px-4 py-2 rounded-md border border-border text-sm font-medium hover:bg-muted transition-colors"
          >
            Passwort ändern
          </button>
        </div>
      </div>

      <!-- WG-Einstellungen -->
      <div
        v-if="hasApartment"
        class="rounded-xl border border-border bg-card p-6"
      >
        <div class="flex items-center gap-3 mb-6">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent/50">
            <Home class="h-5 w-5 text-primary" />
          </div>

          <div>
            <h2 class="text-lg font-semibold">WG-Einstellungen</h2>
            <p class="text-sm text-muted-foreground">
              Allgemeine WG-Informationen
            </p>
          </div>
        </div>

        <div class="space-y-4">
          <div class="space-y-2">
            <label class="text-sm font-medium">WG-Name</label>
            <input
              v-model="wg.name"
              readonly
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Adresse</label>
            <input
              v-model="wg.address"
              :readonly="!isAdmin"
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring disabled:opacity-60"
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Währung</label>
            <select
              v-model="wg.currency"
              :disabled="!isAdmin"
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring disabled:opacity-60"
            >
              <option value="EUR">Euro (EUR)</option>
              <option value="USD">US Dollar (USD)</option>
              <option value="CHF">Schweizer Franken (CHF)</option>
              <option value="GBP">Britisches Pfund (GBP)</option>
            </select>
          </div>

          <p
            v-if="!isAdmin"
            class="text-xs text-muted-foreground"
          >
            Nur Admins können Adresse und Währung ändern.
          </p>
        </div>
      </div>

      <!-- Benachrichtigungen -->
      <div class="rounded-xl border border-border bg-card p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/20">
            <Bell class="h-5 w-5 text-warning-foreground" />
          </div>

          <div>
            <h2 class="text-lg font-semibold">Benachrichtigungen</h2>
            <p class="text-sm text-muted-foreground">
              Benachrichtigungseinstellungen verwalten
            </p>
          </div>
        </div>

        <div class="space-y-4 divide-y divide-border">
          <div
            v-for="item in notifItems"
            :key="item.key"
            class="flex items-center justify-between py-3 first:pt-0 last:pb-0"
          >
            <div>
              <p class="font-medium">{{ item.label }}</p>
              <p class="text-sm text-muted-foreground">{{ item.desc }}</p>
            </div>

            <button
              type="button"
              :class="[
                'relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors',
                notifs[item.key] ? 'bg-primary' : 'bg-muted'
              ]"
              @click="notifs[item.key] = !notifs[item.key]"
            >
              <span
                :class="[
                  'inline-block h-5 w-5 rounded-full bg-white shadow transition-transform',
                  notifs[item.key] ? 'translate-x-5' : 'translate-x-0.5'
                ]"
              />
            </button>
          </div>
        </div>
      </div>

      <!-- Save -->
      <div class="flex justify-end">
        <div class="space-y-2 text-right">
          <p v-if="saveMessage" class="text-sm text-green-600">
            {{ saveMessage }}
          </p>

          <p v-if="errorMessage" class="text-sm text-red-600">
            {{ errorMessage }}
          </p>

          <button
            type="button"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors"
            @click="saveSettings"
          >
            <Save class="h-4 w-4" />
            Änderungen speichern
          </button>
        </div>
      </div>

      <!-- Achtung -->
      <div
        v-if="hasApartment"
        class="rounded-xl border border-red-200 bg-red-50 p-6"
      >
        <h2 class="text-lg font-semibold text-red-700">
          Achtung!
        </h2>

        <p class="mt-1 text-sm text-red-600">
          Du kannst die WG verlassen. Wenn du Admin bist, dann wird automatisch ein anderes Mitglied Admin.
        </p>

        <button
          type="button"
          class="mt-2 rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 transition-colors"
          @click="leaveApartment"
        >
          WG verlassen
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { User, Lock, Home, Bell, Save } from 'lucide-vue-next'
import AppNavbar from '@/components/AppNavbar.vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const isAdmin = computed(() => {
  return authStore.currentUser?.role === 'admin'
})

const hasApartment = computed(() => {
  return !!authStore.currentUser?.apartment_id
})

const profile = reactive({
  name: '',
  email: '',
  avatar: '',
})

const wg = reactive({
  name: '',
  address: '',
  currency: 'EUR',
})

const notifs = reactive({
  email: true,
  push: false,
  tasks: true,
  expenses: true,
})

const saveMessage = ref('')
const errorMessage = ref('')

const notifItems = [
  {
    key: 'email',
    label: 'E-Mail Benachrichtigungen',
    desc: 'Erhalte Updates per E-Mail',
  },
  {
    key: 'push',
    label: 'Push Benachrichtigungen',
    desc: 'Browser Push-Nachrichten',
  },
  {
    key: 'tasks',
    label: 'Aufgaben-Erinnerungen',
    desc: 'Erinnerungen für anstehende Aufgaben',
  },
  {
    key: 'expenses',
    label: 'Ausgaben-Updates',
    desc: 'Benachrichtigungen bei neuen Ausgaben',
  },
]

function loadProfileData() {
  profile.name = authStore.currentUser?.name || ''
  profile.email = authStore.currentUser?.email || ''
  profile.avatar =
    authStore.currentUser?.avatar ||
    authStore.currentUser?.name?.charAt(0).toUpperCase() ||
    ''
}

function loadApartmentData() {
  wg.name = authStore.currentUser?.apartment?.name || ''
  wg.address = authStore.currentUser?.apartment?.address || ''
  wg.currency = authStore.currentUser?.apartment?.currency || 'EUR'
}

onMounted(() => {
  authStore.loadUser()

  if (!authStore.currentUser) {
    router.push('/login')
    return
  }

  loadProfileData()
  loadApartmentData()

  const savedSettings = localStorage.getItem('settings')

  if (savedSettings) {
    const settings = JSON.parse(savedSettings)

    if (settings.notifs) {
      Object.assign(notifs, settings.notifs)
    }
  }
})

async function leaveApartment() {
  const confirmed = confirm('Möchtest du diese WG wirklich verlassen?')

  if (!confirmed) {
    return
  }

  try {
    const response = await authStore.leaveApartment()

    localStorage.removeItem('settings')
    loadApartmentData()

    saveMessage.value = response.message || 'Du hast die WG verlassen.'

    router.push('/wg-auswahl')
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message || 'WG konnte nicht verlassen werden.'
  }
}

async function saveSettings() {
  errorMessage.value = ''
  saveMessage.value = ''

  profile.avatar = profile.name.charAt(0).toUpperCase()

  try {
    let savedUser = authStore.currentUser

    if (isAdmin.value) {
      savedUser = await authStore.updateApartmentSettings({
        address: wg.address,
        currency: wg.currency,
      })
    }

    if (savedUser) {
      const finalUser = {
        ...savedUser,
        name: profile.name,
        email: profile.email,
        avatar: profile.avatar,
      }

      authStore.currentUser = finalUser
      localStorage.setItem('currentUser', JSON.stringify(finalUser))

      wg.name = finalUser.apartment?.name || ''
      wg.address = finalUser.apartment?.address || ''
      wg.currency = finalUser.apartment?.currency || 'EUR'
    }

    localStorage.setItem(
      'settings',
      JSON.stringify({
        notifs,
      })
    )

    saveMessage.value = 'Änderungen wurden gespeichert.'

    setTimeout(() => {
      saveMessage.value = ''
    }, 2000)
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message || 'Einstellungen konnten nicht gespeichert werden.'
  }
}
</script>