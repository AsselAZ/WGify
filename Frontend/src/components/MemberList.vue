<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h3 class="text-lg font-semibold">WG-Mitglieder</h3>

      <button
        type="button"
        class="inline-flex items-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors"
        @click="showInviteDialog = true"
      >
        <UserPlus class="h-4 w-4" />
        Mitglied einladen
      </button>
    </div>

    <div class="space-y-3">
      <div
        v-for="member in members"
        :key="member.id"
        class="flex items-center gap-4 rounded-lg border border-border bg-background p-4"
      >
        <div
          class="flex h-10 w-10 items-center justify-center rounded-full bg-purple text-purple-foreground font-semibold"
        >
          {{ member.avatar || member.name.charAt(0).toUpperCase() }}
        </div>

        <div class="flex-1 min-w-0">
          <p class="font-medium truncate">
            {{ member.name }}
          </p>
          <p class="text-sm text-muted-foreground truncate">
            {{ member.email }}
          </p>
        </div>

        <div
          class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium"
          :class="member.role === 'admin'
            ? 'bg-yellow-100 text-yellow-700'
            : 'bg-muted text-muted-foreground'"
        >
          <Crown v-if="member.role === 'admin'" class="h-3 w-3" />
          <User v-else class="h-3 w-3" />
          {{ member.role === 'admin' ? 'Admin' : 'Mitglied' }}
        </div>
        <button
          v-if="isAdmin && String(member.id) !== currentUserId"
          type="button"
          class="rounded-md p-2 text-red-600 hover:bg-red-50 transition-colors"
          title="Mitglied entfernen"
          @click="removeMember(member)"
        >
          <Trash2 class="h-4 w-4" />
        </button>
      </div>

      <p
        v-if="members.length === 0"
        class="text-sm text-muted-foreground text-center py-8"
      >
        Noch keine Mitglieder vorhanden.
      </p>
    </div>

    <div
      v-if="showInviteDialog"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
      @click.self="showInviteDialog = false"
    >
      <div class="w-full max-w-md rounded-xl border border-border bg-card p-6 shadow-lg">
        <div class="flex items-start justify-between gap-4">
          <div>
            <h3 class="text-lg font-semibold">Mitglied einladen</h3>
            <p class="mt-1 text-sm text-muted-foreground">
              Neue Mitglieder wählen bei der Registrierung „WG beitreten“ und geben diesen Code ein.
            </p>
          </div>

          <button
            type="button"
            class="rounded-md p-1 text-muted-foreground hover:bg-muted hover:text-foreground"
            @click="showInviteDialog = false"
          >
            <X class="h-5 w-5" />
          </button>
        </div>

        <div class="mt-5 space-y-2">
          <label class="text-sm font-medium">Einladungscode</label>

          <div class="flex gap-2">
            <input
              :value="inviteCode"
              readonly
              class="w-full rounded-md border border-border bg-input px-3 py-2 text-sm font-mono font-semibold"
            />

            <button
              type="button"
              class="rounded-md bg-primary px-3 py-2 text-sm font-medium text-primary-foreground"
              @click="copyInviteCode"
            >
              Kopieren
            </button>
          </div>

          <p v-if="copied" class="text-sm text-green-600">
            Code kopiert.
          </p>
        </div>

        <div class="mt-6 flex justify-end">
          <button
            type="button"
            class="rounded-md border border-border px-4 py-2 text-sm font-medium hover:bg-muted transition-colors"
            @click="showInviteDialog = false"
          >
            Schließen
          </button>
        </div>
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
</template>

<script setup>
import AppConfirmDialog from '@/components/AppConfirmDialog.vue'
import { computed, ref } from 'vue'
import {
  UserPlus,
  Crown,
  User,
  X,
  Trash2,
} from 'lucide-vue-next'

import { useAuthStore } from '@/stores/auth'

defineProps({
  members: {
    type: Array,
    required: true,
  },
})

const showInviteDialog = ref(false)
const copied = ref(false)
const showRemoveDialog = ref(false)
const selectedMember = ref(null)

const emit = defineEmits(['removeMember'])

const authStore = useAuthStore()

const isAdmin = computed(() => {
  return authStore.currentUser?.role === 'admin'
})

const currentUserId = computed(() => {
  return String(authStore.currentUser?.id || '')
})

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

const currentUser = computed(() => {
  const savedUser = localStorage.getItem('currentUser')

  if (!savedUser) {
    return null
  }

  return JSON.parse(savedUser)
})

const inviteCode = computed(() => {
  return currentUser.value?.apartment?.inviteCode || ''
})

async function copyInviteCode() {
  await navigator.clipboard.writeText(inviteCode.value)
  copied.value = true

  setTimeout(() => {
    copied.value = false
  }, 2000)
}
</script>