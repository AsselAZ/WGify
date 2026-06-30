<template>
  <div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h2 class="text-lg font-semibold">
        Alle Aufgaben
      </h2>

      <button
        type="button"
        class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 sm:w-auto"
        @click="openCreateDialog"
      >
        <Plus class="h-4 w-4" />
        Neue Aufgabe
      </button>
    </div>

    <!-- Mobile Ansicht -->
    <div class="space-y-3 xl:hidden">
      <div
        v-for="task in tasks"
        :key="task.id"
        :class="[
          'rounded-xl border border-border bg-background p-4 shadow-sm',
          task.status === 'erledigt' ? 'opacity-60' : '',
        ]"
      >
        <div class="flex items-start gap-3">
          <input
            type="checkbox"
            :checked="task.status === 'erledigt'"
            class="mt-1 h-4 w-4 cursor-pointer rounded border-border accent-primary"
            @change="toggleStatus(task)"
          />

          <div class="min-w-0 flex-1">
            <h3
              :class="[
                'font-semibold',
                task.status === 'erledigt' ? 'line-through' : '',
              ]"
            >
              {{ task.title }}
            </h3>

            <p class="mt-1 text-sm text-muted-foreground">
              {{ task.assignedTo }} · Fällig: {{ formatDate(task.dueDate) }}
            </p>
          </div>
        </div>

        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <button
            type="button"
            :class="[
              'inline-flex w-fit items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-medium transition-colors',
              task.status === 'erledigt'
                ? 'bg-accent/50 text-accent-foreground hover:bg-accent/70'
                : 'bg-warning/20 text-warning-foreground hover:bg-warning/30',
            ]"
            @click="toggleStatus(task)"
          >
            <CheckCircle2
              v-if="task.status === 'erledigt'"
              class="h-3 w-3"
            />

            <Circle
              v-else
              class="h-3 w-3"
            />

            {{ task.status === 'erledigt' ? 'Erledigt' : 'Offen' }}
          </button>

          <div class="flex flex-wrap gap-2">
            <button
              type="button"
              class="inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs transition-colors hover:bg-muted"
              @click="openEditDialog(task)"
            >
              <Pencil class="h-3.5 w-3.5" />
              Bearbeiten
            </button>

            <button
              type="button"
              class="inline-flex items-center gap-1 rounded-md border border-red-200 px-2 py-1 text-xs text-red-600 transition-colors hover:bg-red-50"
              @click="openDeleteDialog(task)"
            >
              <Trash2 class="h-3.5 w-3.5" />
              Löschen
            </button>
          </div>
        </div>
      </div>

      <div
        v-if="tasks.length === 0"
        class="rounded-xl border border-border bg-card px-4 py-8 text-center text-sm text-muted-foreground"
      >
        Noch keine Aufgaben vorhanden.
      </div>
    </div>

    <!-- Desktop Ansicht -->
    <div class="hidden rounded-xl border border-border overflow-x-auto xl:block">
      <table class="w-full min-w-[760px] text-sm">
        <thead>
          <tr class="bg-accent/30">
            <th class="w-12 px-4 py-3"></th>
            <th class="px-4 py-3 text-left font-medium text-muted-foreground">
              Aufgabe
            </th>
            <th class="px-4 py-3 text-left font-medium text-muted-foreground">
              Zuständig
            </th>
            <th class="px-4 py-3 text-left font-medium text-muted-foreground">
              Fällig am
            </th>
            <th class="px-4 py-3 text-left font-medium text-muted-foreground">
              Status
            </th>
            <th class="px-4 py-3 text-right font-medium text-muted-foreground">
              Aktionen
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="task in tasks"
            :key="task.id"
            :class="[
              'border-t border-border transition-colors hover:bg-muted/30',
              task.status === 'erledigt' ? 'opacity-60' : '',
            ]"
          >
            <td class="px-4 py-3">
              <input
                type="checkbox"
                :checked="task.status === 'erledigt'"
                class="h-4 w-4 cursor-pointer rounded border-border accent-primary"
                @change="toggleStatus(task)"
              />
            </td>

            <td
              :class="[
                'px-4 py-3 font-medium',
                task.status === 'erledigt' ? 'line-through' : '',
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
                    : 'bg-warning/20 text-warning-foreground hover:bg-warning/30',
                ]"
                @click="toggleStatus(task)"
              >
                <CheckCircle2
                  v-if="task.status === 'erledigt'"
                  class="h-3 w-3"
                />

                <Circle
                  v-else
                  class="h-3 w-3"
                />

                {{ task.status === 'erledigt' ? 'Erledigt' : 'Offen' }}
              </button>
            </td>

            <td class="px-4 py-3">
              <div class="flex justify-end gap-2">
                <button
                  type="button"
                  class="inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs transition-colors hover:bg-muted"
                  @click="openEditDialog(task)"
                >
                  <Pencil class="h-3.5 w-3.5" />
                  Bearbeiten
                </button>

                <button
                  type="button"
                  class="inline-flex items-center gap-1 rounded-md border border-red-200 px-2 py-1 text-xs text-red-600 transition-colors hover:bg-red-50"
                  @click="openDeleteDialog(task)"
                >
                  <Trash2 class="h-3.5 w-3.5" />
                  Löschen
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="tasks.length === 0">
            <td
              colspan="6"
              class="px-4 py-8 text-center text-muted-foreground"
            >
              Noch keine Aufgaben vorhanden.
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Formular Dialog -->
    <div
      v-if="showDialog"
      class="fixed inset-0 z-50 flex items-end justify-center bg-black/50 p-3 sm:items-center sm:p-4"
      @click.self="closeDialog"
    >
      <div class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-t-2xl border border-border bg-card p-4 shadow-xl sm:rounded-xl sm:p-6">
        <h3 class="mb-4 text-lg font-semibold">
          {{ editingTaskId ? 'Aufgabe bearbeiten' : 'Neue Aufgabe hinzufügen' }}
        </h3>

        <form
          class="space-y-4"
          novalidate
          @submit.prevent="handleSubmit"
        >
          <div class="space-y-2">
            <label class="text-sm font-medium">Aufgabe</label>

            <input
              v-model="form.title"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
              placeholder="z.B. Küche putzen"
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Zuständig</label>

            <select
              v-model="form.assignedTo"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
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
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Status</label>

            <select
              v-model="form.status"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            >
              <option value="offen">Offen</option>
              <option value="erledigt">Erledigt</option>
            </select>
          </div>

          <p
            v-if="formError"
            class="rounded-md bg-red-50 px-3 py-2 text-sm text-red-600"
          >
            {{ formError }}
          </p>

          <div class="flex flex-col gap-3 pt-2 sm:flex-row">
            <button
              type="button"
              class="h-10 flex-1 rounded-md border border-border px-4 text-sm font-medium transition-colors hover:bg-muted"
              @click="closeDialog"
            >
              Abbrechen
            </button>

            <button
              type="submit"
              class="h-10 flex-1 rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90"
            >
              {{ editingTaskId ? 'Speichern' : 'Hinzufügen' }}
            </button>
          </div>
        </form>
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
  </div>
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
  formError.value = ''

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
  formError.value = ''

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
  formError.value = ''
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
    title: form.value.title.trim(),
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

function openDeleteDialog(task) {
  selectedTask.value = task
  showDeleteDialog.value = true
}

function confirmDeleteTask() {
  if (!selectedTask.value) {
    return
  }

  emit('deleteTask', selectedTask.value.id)
  selectedTask.value = null
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