<template>
  <div class="min-h-screen flex items-center justify-center bg-background p-4">
    <div class="w-full max-w-xl rounded-xl border border-border bg-card p-6 shadow-sm">
      <div class="text-center">
        <h1 class="text-2xl font-bold">WG auswählen</h1>

        <p class="mt-2 text-sm text-muted-foreground">
          Du bist aktuell in keiner WG. Erstelle eine neue WG oder tritt einer bestehenden WG bei.
        </p>
      </div>

      <div class="mt-6 grid grid-cols-2 gap-2">
        <button
          type="button"
          class="rounded-md border px-4 py-2 text-sm font-medium transition-colors"
          :class="mode === 'create'
            ? 'bg-primary text-primary-foreground border-primary'
            : 'border-border hover:bg-muted'"
          @click="mode = 'create'"
        >
          Neue WG erstellen
        </button>

        <button
          type="button"
          class="rounded-md border px-4 py-2 text-sm font-medium transition-colors"
          :class="mode === 'join'
            ? 'bg-primary text-primary-foreground border-primary'
            : 'border-border hover:bg-muted'"
          @click="mode = 'join'"
        >
          WG beitreten
        </button>
      </div>

      <form class="mt-6 space-y-4" @submit.prevent="submit">
        <div v-if="mode === 'create'" class="space-y-2">
          <label class="text-sm font-medium">WG-Name</label>

          <input
            v-model="apartmentName"
            placeholder="z.B. Haus am See"
            class="w-full rounded-md border border-border bg-input px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-ring"
          />
        </div>

        <div v-else class="space-y-2">
          <label class="text-sm font-medium">Einladungscode</label>

          <input
            v-model="inviteCode"
            placeholder="z.B. ABC123"
            class="w-full rounded-md border border-border bg-input px-3 py-2 text-sm uppercase outline-none focus:ring-2 focus:ring-ring"
          />
        </div>

        <p v-if="errorMessage" class="text-sm text-red-600">
          {{ errorMessage }}
        </p>

        <button
          type="submit"
          class="w-full rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground hover:bg-primary/90 transition-colors"
        >
          {{ mode === 'create' ? 'WG erstellen' : 'WG beitreten' }}
        </button>

        <button
          type="button"
          class="w-full rounded-md border border-border px-4 py-2 text-sm font-medium hover:bg-muted transition-colors"
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

const router = useRouter()
const authStore = useAuthStore()

const mode = ref('join')
const apartmentName = ref('')
const inviteCode = ref('')
const errorMessage = ref('')

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
  errorMessage.value = ''

  try {
    if (mode.value === 'create') {
      await authStore.createApartment(apartmentName.value)
    } else {
      await authStore.joinApartment(inviteCode.value)
    }

    router.push(appHome)
  } catch (error) {
    const errors = error.response?.data?.errors

    errorMessage.value =
      errors?.apartmentName?.[0] ||
      errors?.inviteCode?.[0] ||
      errors?.apartment?.[0] ||
      error.response?.data?.message ||
      'Aktion fehlgeschlagen.'
  }
}

function logout() {
  authStore.logout()
  router.push('/login')
}
</script>