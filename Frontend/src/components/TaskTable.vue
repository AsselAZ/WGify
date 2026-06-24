<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">Alle Aufgaben</h2>
      <button
        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors"
        @click="showDialog = true"
      >
        <Plus class="h-4 w-4" />
        Neue Aufgabe
      </button>
    </div>

    <div class="rounded-xl border border-border overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-accent/30">
            <th class="w-12 px-4 py-3"></th>
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Aufgabe</th>
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Zuständig</th>
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Fällig am</th>
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="task in tasks"
            :key="task.id"
            :class="['border-t border-border hover:bg-muted/30 transition-colors', task.status === 'erledigt' ? 'opacity-60' : '']"
          >
            <td class="px-4 py-3">
              <input
                type="checkbox"
                :checked="task.status === 'erledigt'"
                @change="emit('toggleStatus', task.id)"
                class="w-4 h-4 rounded border-border accent-primary cursor-pointer"
              />
            </td>
            <td :class="['px-4 py-3 font-medium', task.status === 'erledigt' ? 'line-through' : '']">
              {{ task.title }}
            </td>
            <td class="px-4 py-3">{{ task.assignedTo }}</td>
            <td class="px-4 py-3 text-muted-foreground">{{ formatDate(task.dueDate) }}</td>
            <td class="px-4 py-3">
              <span :class="['inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium',
                task.status === 'erledigt' ? 'bg-accent/50 text-accent-foreground' : 'bg-warning/20 text-warning-foreground']">
                <CheckCircle2 v-if="task.status === 'erledigt'" class="h-3 w-3" />
                <Circle v-else class="h-3 w-3" />
                {{ task.status === 'erledigt' ? 'Erledigt' : 'Offen' }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Dialog -->
    <div v-if="showDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-card rounded-xl border border-border p-6 w-full max-w-md mx-4 shadow-xl">
        <h3 class="text-lg font-semibold mb-4">Neue Aufgabe hinzufügen</h3>
        <form class="space-y-4" @submit.prevent="handleSubmit">
          <div class="space-y-2">
            <label class="text-sm font-medium">Aufgabe</label>
            <input v-model="form.title" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" placeholder="z.B. Küche putzen" />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Zuständig</label>
            <select v-model="form.assignedTo" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring">
              <option value="">Person wählen</option>
              <option v-for="m in members" :key="m.id" :value="m.name">{{ m.name }}</option>
            </select>
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Fällig am</label>
            <input v-model="form.dueDate" type="date" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
          </div>
          <div class="flex gap-3 pt-2">
            <button type="button" class="flex-1 px-4 py-2 rounded-md border border-border text-sm font-medium hover:bg-muted transition-colors" @click="showDialog = false">Abbrechen</button>
            <button type="submit" class="flex-1 px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors">Hinzufügen</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Plus, CheckCircle2, Circle } from 'lucide-vue-next'
import { members } from '@/lib/mockData'

defineProps({
  tasks: {
    type: Array,
    required: true,
  },
})

const emit = defineEmits(['addTask', 'toggleStatus'])

const showDialog = ref(false)

const form = ref({
  title: '',
  assignedTo: '',
  dueDate: new Date().toISOString().split('T')[0],
})

function handleSubmit() {
  if (!form.value.title || !form.value.assignedTo) {
    return
  }

  emit('addTask', {
    title: form.value.title,
    assignedTo: form.value.assignedTo,
    dueDate: form.value.dueDate,
    status: 'offen',
  })

  form.value = {
    title: '',
    assignedTo: '',
    dueDate: new Date().toISOString().split('T')[0],
  }

  showDialog.value = false
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('de-DE')
}
</script>
