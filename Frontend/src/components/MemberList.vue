<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h3 class="text-lg font-semibold">
        WG-Mitglieder
      </h3>

      <button
        type="button"
        class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 sm:w-auto"
        @click="showInviteDialog = true"
      >
        <UserPlus class="h-4 w-4" />
        Mitglied einladen
      </button>
    </div>

    <!-- Member List -->
    <div class="space-y-3">
      <div
        v-for="member in members"
        :key="member.id"
        class="flex flex-col gap-4 rounded-lg border border-border bg-background p-4 sm:flex-row sm:items-center"
      >
        <div class="flex items-center gap-4 sm:flex-1">
          <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-purple font-semibold text-purple-foreground">
            {{ member.avatar || member.name.charAt(0).toUpperCase() }}
          </div>

          <div class="min-w-0 flex-1">
            <p class="truncate font-medium">
              {{ member.name }}
            </p>

            <p class="truncate text-sm text-muted-foreground">
              {{ member.email }}
            </p>
          </div>
        </div>

        <div class="flex items-center justify-between gap-3 sm:justify-end">
          <div
            class="inline-flex w-fit items-center gap-1 rounded-full px-2 py-1 text-xs font-medium"
            :class="member.role === 'admin'
              ? 'bg-yellow-100 text-yellow-700'
              : 'bg-muted text-muted-foreground'"
          >
            <Crown
              v-if="member.role === 'admin'"
              class="h-3 w-3"
            />

            <User
              v-else
              class="h-3 w-3"
            />

            {{ member.role === 'admin' ? 'Admin' : 'Mitglied' }}
          </div>

          <button
            v-if="isAdmin && String(member.id) !== currentUserId"
            type="button"
            class="rounded-md p-2 text-red-600 transition-colors hover:bg-red-50"
            title="Mitglied entfernen"
            @click="removeMember(member)"
          >
            <Trash2 class="h-4 w-4" />
          </button>
        </div>
      </div>

      <p
        v-if="members.length === 0"
        class="py-8 text-center text-sm text-muted-foreground"
      >
        Noch keine Mitglieder vorhanden.
      </p>
    </div>

    <!-- Invite Modal -->
    <div
      v-if="showInviteDialog"
      class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-3 sm:items-center sm:p-4"
      @click.self="showInviteDialog = false"
    >
      <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-t-2xl border border-border bg-card p-4 shadow-lg sm:rounded-xl sm:p-6">
        <!-- Header -->
        <div class="flex items-start justify-between gap-4">
          <div class="min-w-0">
            <h3 class="text-lg font-semibold">
              Mitglied einladen
            </h3>

            <p class="mt-1 text-sm text-muted-foreground">
              Einladung per Code oder E-Mail senden
            </p>
          </div>

          <button
            type="button"
            class="shrink-0 rounded-md p-1 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground"
            @click="showInviteDialog = false"
          >
            <X class="h-5 w-5" />
          </button>
        </div>

        <!-- Invite Code -->
        <div class="mt-5 space-y-2">
          <label class="text-sm font-medium">Einladungscode</label>

          <div class="flex flex-col gap-2 sm:flex-row">
            <input
              :value="inviteCode"
              readonly
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm font-mono font-semibold"
            />

            <button
              type="button"
              class="h-10 w-full rounded-md bg-primary px-3 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 sm:w-auto"
              @click="copyInviteCode"
            >
              Kopieren
            </button>
          </div>

          <p
            v-if="copied"
            class="text-sm text-green-600"
          >
            Code kopiert.
          </p>
        </div>

        <!-- Email Invite -->
        <div class="mt-6 space-y-2">
          <label class="text-sm font-medium">Per E-Mail einladen</label>

          <input
            v-model="inviteEmail"
            type="email"
            placeholder="email@beispiel.de"
            class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
          />

          <button
            type="button"
            class="h-10 w-full rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 disabled:opacity-60"
            :disabled="sending"
            @click="sendInvite"
          >
            {{ sending ? 'Sende Einladung...' : 'Einladung senden' }}
          </button>

          <p
            v-if="successMessage"
            class="text-sm text-green-600"
          >
            {{ successMessage }}
          </p>

          <p
            v-if="errorMessage"
            class="text-sm text-red-600"
          >
            {{ errorMessage }}
          </p>
        </div>

        <!-- Close -->
        <div class="mt-6 flex justify-stretch sm:justify-end">
          <button
            type="button"
            class="h-10 w-full rounded-md border border-border px-4 text-sm font-medium transition-colors hover:bg-muted sm:w-auto"
            @click="showInviteDialog = false"
          >
            Schließen
          </button>
        </div>
      </div>
    </div>

    <AppConfirmDialog
      v-model="showRemoveDialog"
      title="Mitglied entfernen?"
      :message="`${selectedMember?.name || 'Dieses Mitglied'} wird aus der WG entfernt und kann danach nicht mehr auf diese WG zugreifen.`"
      confirm-text="Entfernen"
      cancel-text="Abbrechen"
      @confirm="confirmRemoveMember"
    />
  </div>
</template>

<script setup>
import AppConfirmDialog from '@/components/AppConfirmDialog.vue'
import { computed, onMounted, onUnmounted, ref } from 'vue'
import {
  UserPlus,
  Crown,
  User,
  X,
  Trash2,
} from 'lucide-vue-next'

import { useAuthStore } from '@/stores/auth'
import { useMembersStore } from '@/stores/members'
import { api } from '@/lib/api'

defineProps({
  members: {
    type: Array,
    required: true,
  },
})

const emit = defineEmits(['removeMember'])

const authStore = useAuthStore()
const membersStore = useMembersStore()

const isAdmin = computed(() => authStore.currentUser?.role === 'admin')

const currentUserId = computed(() => {
  return String(authStore.currentUser?.id || '')
})

const showInviteDialog = ref(false)
const showRemoveDialog = ref(false)
const selectedMember = ref(null)

const liveInviteCode = ref('')
const inviteCodeTimer = ref(null)
const copied = ref(false)

const inviteEmail = ref('')
const sending = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const currentUser = computed(() => {
  const savedUser = localStorage.getItem('currentUser')
  return savedUser ? JSON.parse(savedUser) : null
})

const inviteCode = computed(() => {
  return liveInviteCode.value || currentUser.value?.apartment?.inviteCode || ''
})

onMounted(() => {
  refreshInviteCode()

  inviteCodeTimer.value = setInterval(() => {
    refreshInviteCode()
  }, 10000)
})

onUnmounted(() => {
  if (inviteCodeTimer.value) {
    clearInterval(inviteCodeTimer.value)
  }
})

async function refreshInviteCode() {
  try {
    const data = await membersStore.loadInviteCode()
    liveInviteCode.value = data.inviteCode
  } catch (error) {
    console.log('Invite code konnte nicht geladen werden:', error)
  }
}

function removeMember(member) {
  selectedMember.value = member
  showRemoveDialog.value = true
}

function confirmRemoveMember() {
  if (!selectedMember.value) {
    return
  }

  emit('removeMember', selectedMember.value.id)
  selectedMember.value = null
}

async function copyInviteCode() {
  await navigator.clipboard.writeText(inviteCode.value)
  copied.value = true

  setTimeout(() => {
    copied.value = false
  }, 2000)
}

async function sendInvite() {
  sending.value = true
  successMessage.value = ''
  errorMessage.value = ''

  try {
    await api.post('/apartments/invite', {
      email: inviteEmail.value,
    })

    await membersStore.loadPendingInvitationsCount()

    successMessage.value = 'Einladung wurde gesendet!'
    inviteEmail.value = ''
  } catch (err) {
    console.log(err.response?.data || err)
    errorMessage.value =
      err.response?.data?.message || 'Fehler beim Senden der Einladung'
  } finally {
    sending.value = false
  }
}
</script>