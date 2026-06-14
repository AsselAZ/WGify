<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">WG-Mitglieder</h2>
      <button
        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors"
        @click="showDialog = true"
      >
        <Plus class="h-4 w-4" />
        Mitglied einladen
      </button>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div
        v-for="(member, index) in members"
        :key="member.id"
        class="flex items-center gap-4 rounded-xl border border-border bg-card p-4 transition-colors hover:bg-card/80"
      >
        <div :class="['flex h-12 w-12 items-center justify-center rounded-full text-lg font-semibold', getMemberColor(index)]">
          {{ member.avatar }}
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2">
            <p class="font-medium text-card-foreground truncate">{{ member.name }}</p>
            <Crown v-if="member.role === 'admin'" class="h-4 w-4 text-purple flex-shrink-0" />
          </div>
          <p class="text-sm text-muted-foreground truncate">{{ member.email }}</p>
        </div>
        <span :class="['inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium flex-shrink-0',
          member.role === 'admin' ? 'bg-purple/15 text-purple' : 'bg-secondary text-secondary-foreground']">
          <Crown v-if="member.role === 'admin'" class="h-3 w-3" />
          <User v-else class="h-3 w-3" />
          {{ member.role === 'admin' ? 'Admin' : 'Mitglied' }}
        </span>
      </div>
    </div>

    <!-- Dialog -->
    <div v-if="showDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-card rounded-xl border border-border p-6 w-full max-w-md mx-4 shadow-xl">
        <h3 class="text-lg font-semibold mb-4">Neues Mitglied einladen</h3>
        <form class="space-y-4" @submit.prevent="handleSubmit">
          <div class="space-y-2">
            <label class="text-sm font-medium">Name</label>
            <input v-model="form.name" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" placeholder="z.B. Max Mustermann" />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium">E-Mail</label>
            <input v-model="form.email" type="email" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" placeholder="max@beispiel.de" />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Rolle</label>
            <select v-model="form.role" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring">
              <option value="mitglied">Mitglied</option>
              <option value="admin">Admin</option>
            </select>
          </div>
          <div class="flex gap-3 pt-2">
            <button type="button" class="flex-1 px-4 py-2 rounded-md border border-border text-sm font-medium hover:bg-muted transition-colors" @click="showDialog = false">Abbrechen</button>
            <button type="submit" class="flex-1 px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors">Einladung senden</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Plus, Crown, User } from 'lucide-vue-next'
import type { Member } from '@/lib/mockData'

defineProps<{ members: Member[] }>()
const emit = defineEmits<{ addMember: [member: Omit<Member, 'id' | 'avatar'>] }>()

const showDialog = ref(false)
const form = ref({ name: '', email: '', role: 'mitglied' as 'admin' | 'mitglied' })

function handleSubmit() {
  if (!form.value.name || !form.value.email) return
  emit('addMember', { name: form.value.name, email: form.value.email, role: form.value.role })
  form.value = { name: '', email: '', role: 'mitglied' }
  showDialog.value = false
}

const colors = ['bg-primary text-primary-foreground', 'bg-accent text-accent-foreground', 'bg-purple text-purple-foreground', 'bg-secondary text-secondary-foreground']
function getMemberColor(index: number) {
  return colors[index % colors.length]
}
</script>
