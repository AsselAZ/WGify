<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">Alle Aufgaben</h2>

      <button
        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors"
        @click="openCreateDialog"
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
            <th class="text-right px-4 py-3 font-medium text-muted-foreground">Aktionen</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="task in tasks"
            :key="task.id"
            :class="[
              'border-t border-border hover:bg-muted/30 transition-colors',
              task.status === 'erledigt' ? 'opacity-60' : ''
            ]"
          >
            <td class="px-4 py-3">
              <input
                type="checkbox"
                :checked="task.status === 'erledigt'"
                @change="toggleStatus(task)"
                class="w-4 h-4 rounded border-border accent-primary cursor-pointer"
              />
            </td>

            <td
              :class="[
                'px-4 py-3 font-medium',
                task.status === 'erledigt' ? 'line-through' : ''
              ]"
            >
              {{ task.title }}
            </td>

            <td class="px-4 py-3">
              {{ task.assignedTo }}
            </td>

            <td class="px-4 py-3 text-muted-foreground">
              {{ formatDate(task.dueDate) }}
            </td>

            <td class="px-4 py-3">
              <button
                type="button"
                :class="[
                  'inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium transition-colors',
                  task.status === 'erledigt'
                    ? 'bg-accent/50 text-accent-foreground hover:bg-accent/70'
                    : 'bg-warning/20 text-warning-foreground hover:bg-warning/30'
                ]"
                @click="toggleStatus(task)"
              >
                <CheckCircle2 v-if="task.status === 'erledigt'" class="h-3 w-3" />
                <Circle v-else class="h-3 w-3" />
                {{ task.status === 'erledigt' ? 'Erledigt' : 'Offen' }}
              </button>
            </td>

            <td class="px-4 py-3">
              <div class="flex justify-end gap-2">
                <button
                  type="button"
                  class="inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs hover:bg-muted transition-colors"
                  @click="openEditDialog(task)"
                >
                  <Pencil class="h-3.5 w-3.5" />
                  Bearbeiten
                </button>

                <button
                  type="button"
                  class="inline-flex items-center gap-1 rounded-md border border-red-200 px-2 py-1 text-xs text-red-600 hover:bg-red-50 transition-colors"
                  @click="deleteTask(task)"
                >
                  <Trash2 class="h-3.5 w-3.5" />
                  Löschen
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="tasks.length === 0">
            <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
              Noch keine Aufgaben vorhanden.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div
      v-if="showDialog"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
      @click.self="closeDialog"
    >
      <div class="bg-card rounded-xl border border-border p-6 w-full max-w-md mx-4 shadow-xl">
        <h3 class="text-lg font-semibold mb-4">
          {{ editingTaskId ? 'Aufgabe bearbeiten' : 'Neue Aufgabe hinzufügen' }}
        </h3>

        <form class="space-y-4" @submit.prevent="handleSubmit" novalidate>
          
          <div class="space-y-2">
            <label class="text-sm font-medium">Aufgabe</label>
            <input
              v-model="form.title"
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              placeholder="z.B. Küche putzen"
              
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Zuständig</label>
            <select
              v-model="form.assignedTo"
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              
            >
              <option value="">Person wählen</option>
              <option
                v-for="member in members"
                :key="member.id"
                :value="member.name"
              >
                {{ member.name }}
              </option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Fällig am</label>
            <input
              v-model="form.dueDate"
              type="date"
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Status</label>
            <select
              v-model="form.status"
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
            >
              <option value="offen">Offen</option>
              <option value="erledigt">Erledigt</option>
            </select>
          </div>
          <p
  v-if="formError"
  class="mb-3 rounded-md bg-red-50 px-3 py-2 text-sm text-red-600"
>
  {{ formError }}
</p>
          <div class="flex gap-3 pt-2">
            <button
              type="button"
              class="flex-1 px-4 py-2 rounded-md border border-border text-sm font-medium hover:bg-muted transition-colors"
              @click="closeDialog"
            >
              Abbrechen
            </button>

            <button
              type="submit"
              class="flex-1 px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors"
            >
              {{ editingTaskId ? 'Speichern' : 'Hinzufügen' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <AppConfirmDialog
    v-model="showDeleteDialog"
    title="Aufgabe löschen?"
    :message="`Möchtest du die Aufgabe '${selectedTask?.title || ''}' wirklich löschen?`"
    confirm-text="Löschen"
    cancel-text="Abbrechen"
    @confirm="confirmDeleteTask"
  />
</template>

<script setup>
import AppConfirmDialog from '@/components/AppConfirmDialog.vue'
import { ref } from 'vue'
import {
  Plus,
  CheckCircle2,
  Circle,
  Pencil,
  Trash2,
} from 'lucide-vue-next'

defineProps({
  tasks: {
    type: Array,
    required: true,
  },
  members: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits([
  'createTask',
  'updateTask',
  'deleteTask',
  'toggleTaskStatus',
])

const showDialog = ref(false)
const editingTaskId = ref(null)
const formError = ref('')
const showDeleteDialog = ref(false)
const selectedTask = ref(null)

const form = ref({
  title: '',
  assignedTo: '',
  dueDate: new Date().toISOString().split('T')[0],
  status: 'offen',
})

function openCreateDialog() {
  editingTaskId.value = null

  form.value = {
    title: '',
    assignedTo: '',
    dueDate: new Date().toISOString().split('T')[0],
    status: 'offen',
  }

  showDialog.value = true
}

function openEditDialog(task) {
  editingTaskId.value = task.id

  form.value = {
    title: task.title,
    assignedTo: task.assignedTo,
    dueDate: task.dueDate,
    status: task.status,
  }

  showDialog.value = true
}

function closeDialog() {
  showDialog.value = false
  editingTaskId.value = null
}

function handleSubmit() {
  formError.value = ''

  if (!form.value.title.trim()) {
    formError.value = 'Bitte gib eine Aufgabe ein.'
    return
  }

  if (!form.value.assignedTo) {
    formError.value = 'Bitte wähle eine zuständige Person aus.'
    return
  }

  if (!form.value.dueDate) {
    formError.value = 'Bitte wähle ein Fälligkeitsdatum aus.'
    return
  }

  const payload = {
    title: form.value.title,
    assignedTo: form.value.assignedTo,
    dueDate: form.value.dueDate,
    status: form.value.status,
  }

  if (editingTaskId.value) {
    emit('updateTask', editingTaskId.value, payload)
  } else {
    emit('createTask', payload)
  }

  closeDialog()
}

function deleteTask(task) {
  selectedTask.value = task
  showDeleteDialog.value = true
}

function toggleStatus(task) {
  const nextStatus = task.status === 'erledigt' ? 'offen' : 'erledigt'

  emit('toggleTaskStatus', task.id, {
    title: task.title,
    assignedTo: task.assignedTo,
    dueDate: task.dueDate,
    status: nextStatus,
  })
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('de-DE')
}
</script>