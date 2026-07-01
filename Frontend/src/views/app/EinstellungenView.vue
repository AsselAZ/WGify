<template>
  <div class="flex min-h-screen w-full flex-col">
    <AppNavbar
      title="Einstellungen"
      subtitle="Verwalte dein Profil und die WG"
      :show-search="false"
    />

    <div class="mx-auto grid w-full max-w-7xl flex-1 grid-cols-1 gap-4 p-4 sm:gap-6 sm:p-6 xl:grid-cols-2 xl:p-8">
      <!-- Profil -->
      <div class="w-full rounded-xl border border-border bg-card p-4 sm:p-6">
        <div class="mb-6 flex items-start gap-3">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-secondary">
            <User class="h-5 w-5 text-primary" />
          </div>

          <div class="min-w-0">
            <h2 class="text-lg font-semibold">Profil</h2>
            <p class="text-sm text-muted-foreground">
              Persönliche Informationen bearbeiten
            </p>
          </div>
        </div>

        <div class="space-y-4">

          <div class="flex items-center gap-4">
            <div
              class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary text-2xl font-semibold text-primary-foreground"
            >
              <span>
                {{ profile.avatar }}
              </span>

            </div>
          </div>

 <!--         
              <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
            <div
              class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-full bg-primary text-2xl font-semibold text-primary-foreground"
            >
              <img
                v-if="avatarUrl"
                :src="avatarUrl"
                alt="Profilbild"
                class="h-full w-full object-cover"
              />

              <span v-else>
                {{ profile.avatar }}
              </span>
            </div>

            <input
              ref="avatarInput"
              type="file"
              accept="image/png,image/jpeg,image/jpg,image/webp"
              class="hidden"
              @change="handleAvatarChange"
            />

            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
              <button
                type="button"
                class="w-full rounded-md border border-border px-4 py-2 text-sm font-medium transition-colors hover:bg-muted sm:w-auto"
                @click="openAvatarPicker"
              >
                Bild ändern
              </button>

              <button
                v-if="avatarUrl"
                type="button"
                class="w-full rounded-md border border-border px-4 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-muted sm:w-auto"
                @click="deleteAvatar"
              >
                Bild löschen
              </button>
            </div>
          </div>
-->
          <div class="grid grid-cols-1 gap-4 2xl:grid-cols-2">
            <div class="space-y-2">
              <label class="text-sm font-medium">Name</label>
              <input
                v-model="profile.name"
                class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">E-Mail</label>
              <input
                v-model="profile.email"
                type="email"
                class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>
          </div>
        </div>
      </div>

      <!-- Passwort -->
      <div class="w-full rounded-xl border border-border bg-card p-4 sm:p-6">
        <div class="mb-6 flex items-start gap-3">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple/15">
            <Lock class="h-5 w-5 text-purple" />
          </div>

          <div class="min-w-0">
            <h2 class="text-lg font-semibold">Passwort</h2>
            <p class="text-sm text-muted-foreground">
              Passwort ändern
            </p>
          </div>
        </div>

        <div class="space-y-4">
          <div class="space-y-2">
            <label class="text-sm font-medium">Aktuelles Passwort</label>
            <input
              v-model="passwordForm.currentPassword"
              type="password"
              name="wgify-current-password"
              autocomplete="new-password"
              placeholder="Aktuelles Passwort eingeben"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            />
          </div>

          <div class="grid grid-cols-1 gap-4 2xl:grid-cols-2">
            <div class="space-y-2">
              <label class="text-sm font-medium">Neues Passwort</label>
              <input
                v-model="passwordForm.newPassword"
                type="password"
                name="wgify-new-password"
                autocomplete="new-password"
                placeholder="Neues Passwort eingeben"
                class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>

            <div class="space-y-2">
              <label class="text-sm font-medium">Passwort bestätigen</label>
              <input
                v-model="passwordForm.confirmPassword"
                type="password"
                name="wgify-confirm-password"
                autocomplete="new-password"
                placeholder="Neues Passwort wiederholen"
                class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
              />
            </div>
          </div>

          <button
            type="button"
            class="w-full rounded-md border border-border px-4 py-2 text-sm font-medium transition-colors hover:bg-muted sm:w-auto"
            @click="changePassword"
          >
            Passwort ändern
          </button>
        </div>
      </div>

      <!-- WG-Einstellungen -->
      <div
        v-if="hasApartment"
        class="w-full rounded-xl border border-border bg-card p-4 sm:p-6"
      >
        <div class="mb-6 flex items-start gap-3">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-accent/50">
            <Home class="h-5 w-5 text-primary" />
          </div>

          <div class="min-w-0">
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
              :readonly="!isAdmin"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Adresse</label>
            <input
              v-model="wg.address"
              :readonly="!isAdmin"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring disabled:opacity-60"
            />
          </div>

          <p
            v-if="!isAdmin"
            class="text-xs text-muted-foreground"
          >
            Nur Admins können WG-Name und Adresse ändern.
          </p>
        </div>
      </div>

      <!-- Benachrichtigungen -->
      <div class="w-full rounded-xl border border-border bg-card p-4 sm:p-6">
        <div class="mb-6 flex items-start gap-3">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-warning/20">
            <Bell class="h-5 w-5 text-warning-foreground" />
          </div>

          <div class="min-w-0">
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
            class="flex flex-col gap-3 py-3 first:pt-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between"
          >
            <div class="min-w-0">
              <p class="font-medium">
                {{ item.label }}
              </p>
              <p class="text-sm text-muted-foreground">
                {{ item.desc }}
              </p>
            </div>

            <button
              type="button"
              :class="[
                'relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors',
                notifs[item.key] ? 'bg-primary' : 'bg-muted',
              ]"
              @click="notifs[item.key] = !notifs[item.key]"
            >
              <span
                :class="[
                  'inline-block h-5 w-5 rounded-full bg-white shadow transition-transform',
                  notifs[item.key] ? 'translate-x-5' : 'translate-x-0.5',
                ]"
              />
            </button>
          </div>
        </div>
      </div>

      <!-- Aktionen -->
      <div class="flex w-full flex-col gap-4 sm:gap-6 xl:col-span-2">
        <div class="rounded-xl border border-border bg-card p-4 sm:p-6">
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h2 class="text-lg font-semibold">
                Änderungen speichern
              </h2>

              <p class="mt-1 text-sm text-muted-foreground">
                Speichere deine Profil- und Benachrichtigungseinstellungen.
              </p>
            </div>

            <button
              type="button"
              class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 sm:w-auto"
              @click="saveSettings"
            >
              <Save class="h-4 w-4" />
              Speichern
            </button>
          </div>
        </div>

        <div
          v-if="hasApartment"
          class="rounded-xl border border-red-200 bg-card p-4 sm:p-6"
        >
          <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
              <h2 class="text-lg font-semibold text-red-700">
                Gefahrenbereich
              </h2>

              <p class="mt-1 max-w-2xl text-sm text-muted-foreground">
                Du kannst die WG verlassen. Wenn du Admin bist, wird automatisch ein anderes Mitglied Admin.
              </p>
            </div>

            <button
              type="button"
              class="inline-flex h-10 w-full items-center justify-center rounded-md border border-red-300 px-4 text-sm font-medium text-red-700 transition-colors hover:bg-red-50 sm:w-auto"
              @click="showLeaveDialog = true"
            >
              WG verlassen
            </button>
          </div>
        </div>
      </div>
    </div>

    <AppConfirmDialog
      v-model="showLeaveDialog"
      title="WG verlassen?"
      message="Möchtest du diese WG wirklich verlassen? Wenn du Admin bist, wird automatisch ein anderes Mitglied Admin."
      confirm-text="WG verlassen"
      cancel-text="Abbrechen"
      @confirm="leaveApartment"
    />
  </div>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import {
  User,
  Lock,
  Home,
  Bell,
  Save,
} from 'lucide-vue-next'
import AppNavbar from '@/components/AppNavbar.vue'
import AppConfirmDialog from '@/components/AppConfirmDialog.vue'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'
import { useMembersStore } from '@/stores/members'
import { useNotificationsStore } from '@/stores/notifications'

const authStore = useAuthStore()
const router = useRouter()
const toast = useToastStore()
const membersStore = useMembersStore()
const notifications = useNotificationsStore()

const showLeaveDialog = ref(false)
//const avatarInput = ref(null)

const avatarUrl = computed(() => {
  if (!authStore.currentUser?.avatar) {
    return null
  }

  return `http://127.0.0.1:8000/storage/${authStore.currentUser.avatar}`
})

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

const passwordForm = reactive({
  currentPassword: '',
  newPassword: '',
  confirmPassword: '',
})

const notifs = reactive({
  email_notifications: true,
  task_reminders: true,
})

const notifItems = [
  {
    key: 'email_notifications',
    label: 'E-Mail Benachrichtigungen',
    desc: 'Erhalte Updates per E-Mail',
  },
  {
    key: 'task_reminders',
    label: 'Aufgaben-Erinnerungen',
    desc: 'Erinnerungen für anstehende Aufgaben',
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

onMounted(async () => {
  await authStore.loadUser()

  if (!authStore.currentUser) {
    router.push('/login')
    return
  }

  loadProfileData()
  loadApartmentData()

  const user = authStore.currentUser

  if (user) {
    notifs.email_notifications = user.email_notifications ?? true
    notifs.task_reminders = user.task_reminders ?? true
  }
})

async function leaveApartment() {
  try {
    await membersStore.loadMembers()

    const leavingUserName = authStore.currentUser?.name || 'Ein Mitglied'
    const leavingUserEmail = authStore.currentUser?.email

    membersStore.members
      .filter((member) => member.email && member.email !== leavingUserEmail)
      .forEach((member) => {
        notifications.addNotificationForUser(
          member.email,
          'member-left',
          `${leavingUserName} hat die WG verlassen.`
        )
      })

    const response = await authStore.leaveApartment()

    localStorage.removeItem('settings')
    loadApartmentData()

    toast.success(
      'WG verlassen',
      response.message || 'Du hast die WG erfolgreich verlassen.'
    )

    router.push('/wg-auswahl')
  } catch (error) {
    toast.error(
      'WG konnte nicht verlassen werden',
      error.response?.data?.message || 'Bitte versuche es erneut.'
    )
  }
}

async function changePassword() {
  if (!passwordForm.currentPassword) {
    toast.error('Aktuelles Passwort fehlt', 'Bitte gib dein aktuelles Passwort ein.')
    return
  }

  if (!passwordForm.newPassword) {
    toast.error('Neues Passwort fehlt', 'Bitte gib ein neues Passwort ein.')
    return
  }

  if (passwordForm.newPassword.length < 8) {
    toast.error('Passwort zu kurz', 'Das neue Passwort muss mindestens 8 Zeichen lang sein.')
    return
  }

  if (passwordForm.newPassword !== passwordForm.confirmPassword) {
    toast.error('Passwörter stimmen nicht überein', 'Bitte bestätige dein neues Passwort korrekt.')
    return
  }

  try {
    await authStore.changePassword({
      currentPassword: passwordForm.currentPassword,
      newPassword: passwordForm.newPassword,
      confirmPassword: passwordForm.confirmPassword,
    })

    passwordForm.currentPassword = ''
    passwordForm.newPassword = ''
    passwordForm.confirmPassword = ''

    toast.success(
      'Passwort geändert',
      'Du kannst dich ab jetzt nur noch mit deinem neuen Passwort anmelden.'
    )
  } catch (error) {
    toast.error(
      'Passwort konnte nicht geändert werden',
      error.response?.data?.errors?.current_password?.[0] ||
        error.response?.data?.errors?.password?.[0] ||
        error.response?.data?.message ||
        'Bitte versuche es erneut.'
    )
  }
}

async function saveSettings() {
  profile.avatar = profile.name?.charAt(0).toUpperCase() || ''

  try {
    let savedUser = authStore.currentUser

    if (isAdmin.value) {
      savedUser = await authStore.updateApartmentSettings({
        name: wg.name.trim(),
        address: wg.address.trim(),
        currency: wg.currency || 'EUR',
      })
    }

    if (savedUser) {
      const finalUser = {
        ...savedUser,
        name: profile.name,
        email: profile.email,
        avatar: savedUser.avatar ?? profile.avatar,
      }

      authStore.currentUser = finalUser
      localStorage.setItem('currentUser', JSON.stringify(finalUser))

      loadProfileData()
      loadApartmentData()
    }

    localStorage.setItem(
      'settings',
      JSON.stringify({
        notifs,
      })
    )

    toast.success(
      'Einstellungen gespeichert',
      'WG-Name und Adresse wurden erfolgreich gespeichert.'
    )
  } catch (error) {
    toast.error(
      'Speichern fehlgeschlagen',
      error.response?.data?.message || 'WG-Daten konnten nicht gespeichert werden.'
    )
  }
}

/*
function openAvatarPicker() {
  avatarInput.value?.click()
}

async function deleteAvatar() {
  await authStore.deleteAvatar()
}

async function handleAvatarChange(event) {
  const file = event.target.files[0]

  if (!file) {
    return
  }

  await authStore.updateAvatar(file)

  event.target.value = ''
}
*/
</script>