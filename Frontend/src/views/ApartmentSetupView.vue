<template>
  <div class="flex min-h-screen items-center justify-center bg-background p-4">
    <div class="w-full max-w-xl rounded-xl border border-border bg-card p-4 shadow-sm sm:p-6">
      <div class="text-center">
        <h1 class="text-2xl font-bold">
          WG auswählen
        </h1>

        <p class="mt-2 text-sm text-muted-foreground">
          Du bist aktuell in keiner WG. Erstelle eine neue WG oder tritt einer bestehenden WG bei.
        </p>
      </div>

      <div class="mt-6 grid grid-cols-1 gap-2 sm:grid-cols-2">
        <button
          type="button"
          class="rounded-md border px-4 py-2 text-sm font-medium transition-colors"
          :class="mode === 'create'
            ? 'border-primary bg-primary text-primary-foreground'
            : 'border-border hover:bg-muted'"
          @click="mode = 'create'"
        >
          Neue WG erstellen
        </button>

        <button
          type="button"
          class="rounded-md border px-4 py-2 text-sm font-medium transition-colors"
          :class="mode === 'join'
            ? 'border-primary bg-primary text-primary-foreground'
            : 'border-border hover:bg-muted'"
          @click="mode = 'join'"
        >
          WG beitreten
        </button>
      </div>

      <form
        class="mt-6 space-y-4"
        @submit.prevent="submit"
      >
        <div
          v-if="mode === 'create'"
          class="space-y-2"
        >
          <label class="text-sm font-medium">WG-Name</label>

          <input
            v-model="apartmentName"
            placeholder="z.B. Haus am See"
            class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
          />
        </div>

        <div
          v-else
          class="space-y-2"
        >
          <label class="text-sm font-medium">Einladungscode</label>

          <input
            v-model="inviteCode"
            placeholder="z.B. ABC123"
            class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm uppercase outline-none focus:ring-2 focus:ring-ring"
          />
        </div>

        <button
          type="submit"
          class="h-10 w-full rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
        >
          {{ mode === 'create' ? 'WG erstellen' : 'WG beitreten' }}
        </button>

        <button
          type="button"
          class="h-10 w-full rounded-md border border-border px-4 text-sm font-medium transition-colors hover:bg-muted"
          @click="logout"
        >
          Abmelden
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useToastStore } from '@/stores/toast'
import { useMembersStore } from '@/stores/members'
import { useNotificationsStore } from '@/stores/notifications'

const router = useRouter()
const authStore = useAuthStore()
const membersStore = useMembersStore()
const notifications = useNotificationsStore()
const toast = useToastStore()

const mode = ref('join')
const apartmentName = ref('')
const inviteCode = ref('')

const appHome = '/app/dashboard'

onMounted(() => {
  authStore.loadUser()

  if (!authStore.currentUser) {
    router.push('/login')
    return
  }

  if (authStore.currentUser.apartment_id) {
    router.push(appHome)
  }
})

async function submit() {
  try {
    if (mode.value === 'create') {
      await authStore.createApartment(apartmentName.value)

      toast.success(
        'WG erstellt',
        'Du wurdest erfolgreich als Admin hinzugefügt.'
      )
    } else {
      await authStore.joinApartment(inviteCode.value)
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

      toast.success(
        'WG beigetreten',
        'Du bist der WG erfolgreich beigetreten.'
      )
    }

    router.push(appHome)
  } catch (error) {
    const errors = error.response?.data?.errors

    toast.error(
      'Aktion fehlgeschlagen',
      errors?.apartmentName?.[0] ||
        errors?.inviteCode?.[0] ||
        errors?.apartment?.[0] ||
        error.response?.data?.message ||
        'Bitte versuche es erneut.'
    )
  }
}

function logout() {
  authStore.logout()
  router.push('/login')
}
</script>