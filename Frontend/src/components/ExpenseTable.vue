<template>
  <div class="space-y-4">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <h2 class="text-lg font-semibold">Alle Ausgaben</h2>

      <button
        type="button"
        class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 sm:w-auto"
        @click="openCreateDialog"
      >
        <Plus class="h-4 w-4" />
        Neue Ausgabe
      </button>
    </div>

    <!-- Mobile Ansicht -->
    <div class="space-y-3 md:hidden">
      <div
        v-for="expense in expenses"
        :key="expense.id"
        class="rounded-xl border border-border bg-card p-4 shadow-sm"
      >
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <h3 class="truncate font-semibold">
              {{ expense.title }}
            </h3>

            <p class="mt-1 text-sm text-muted-foreground">
              {{ formatDate(expense.date) }} · bezahlt von {{ expense.paidBy }}
            </p>
          </div>

          <p class="shrink-0 text-right font-semibold">
            {{ Number(expense.amount).toFixed(2) }} EUR
          </p>
        </div>

        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
          <span
            :class="[
              'inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
              getCategoryColor(expense.category),
            ]"
          >
            {{ expense.category }}
          </span>

          <div class="flex flex-wrap gap-2">
            <button
              type="button"
              class="inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs transition-colors hover:bg-muted"
              @click="openEditDialog(expense)"
            >
              <Pencil class="h-3.5 w-3.5" />
              Bearbeiten
            </button>

            <button
              type="button"
              class="inline-flex items-center gap-1 rounded-md border border-red-200 px-2 py-1 text-xs text-red-600 transition-colors hover:bg-red-50"
              @click="deleteExpense(expense)"
            >
              <Trash2 class="h-3.5 w-3.5" />
              Löschen
            </button>
          </div>
        </div>
      </div>

      <div
        v-if="expenses.length === 0"
        class="rounded-xl border border-border bg-card px-4 py-8 text-center text-sm text-muted-foreground"
      >
        Noch keine Ausgaben vorhanden.
      </div>
    </div>

    <!-- Desktop Ansicht -->
    <div class="hidden rounded-xl border border-border overflow-x-auto md:block">
      <table class="w-full min-w-[760px] text-sm">
        <thead>
          <tr class="bg-secondary/40">
            <th class="px-4 py-3 text-left font-medium text-muted-foreground">
              Titel
            </th>
            <th class="px-4 py-3 text-left font-medium text-muted-foreground">
              Kategorie
            </th>
            <th class="px-4 py-3 text-left font-medium text-muted-foreground">
              Bezahlt von
            </th>
            <th class="px-4 py-3 text-left font-medium text-muted-foreground">
              Datum
            </th>
            <th class="px-4 py-3 text-right font-medium text-muted-foreground">
              Betrag
            </th>
            <th class="px-4 py-3 text-right font-medium text-muted-foreground">
              Aktionen
            </th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="expense in expenses"
            :key="expense.id"
            class="border-t border-border transition-colors hover:bg-muted/30"
          >
            <td class="px-4 py-3 font-medium">
              {{ expense.title }}
            </td>

            <td class="px-4 py-3">
              <span
                :class="[
                  'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                  getCategoryColor(expense.category),
                ]"
              >
                {{ expense.category }}
              </span>
            </td>

            <td class="px-4 py-3">
              {{ expense.paidBy }}
            </td>

            <td class="px-4 py-3 text-muted-foreground">
              {{ formatDate(expense.date) }}
            </td>

            <td class="px-4 py-3 text-right font-semibold">
              {{ Number(expense.amount).toFixed(2) }} EUR
            </td>

            <td class="px-4 py-3">
              <div class="flex justify-end gap-2">
                <button
                  type="button"
                  class="inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs transition-colors hover:bg-muted"
                  @click="openEditDialog(expense)"
                >
                  <Pencil class="h-3.5 w-3.5" />
                  Bearbeiten
                </button>

                <button
                  type="button"
                  class="inline-flex items-center gap-1 rounded-md border border-red-200 px-2 py-1 text-xs text-red-600 transition-colors hover:bg-red-50"
                  @click="deleteExpense(expense)"
                >
                  <Trash2 class="h-3.5 w-3.5" />
                  Löschen
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="expenses.length === 0">
            <td
              colspan="6"
              class="px-4 py-8 text-center text-muted-foreground"
            >
              Noch keine Ausgaben vorhanden.
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
      <div
        class="max-h-[90vh] w-full max-w-md overflow-y-auto rounded-t-2xl border border-border bg-card p-4 shadow-xl sm:rounded-xl sm:p-6"
      >
        <h3 class="mb-4 text-lg font-semibold">
          {{ editingExpenseId ? 'Ausgabe bearbeiten' : 'Neue Ausgabe hinzufügen' }}
        </h3>

        <form
          class="space-y-4"
          novalidate
          @submit.prevent="handleSubmit"
        >
          <div class="space-y-2">
            <label class="text-sm font-medium">Titel</label>
            <input
              v-model="form.title"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
              placeholder="z.B. Internet"
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Betrag (EUR)</label>
            <input
              v-model="form.amount"
              type="number"
              step="0.01"
              min="0"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
              placeholder="0.00"
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Kategorie</label>
            <select
              v-model="form.category"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            >
              <option value="">Kategorie wählen</option>
              <option
                v-for="cat in categories"
                :key="cat"
                :value="cat"
              >
                {{ cat }}
              </option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Bezahlt von</label>
            <select
              v-model="form.paidBy"
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
            <label class="text-sm font-medium">Datum</label>
            <input
              v-model="form.date"
              type="date"
              class="h-10 w-full rounded-md border border-border bg-input px-3 text-sm outline-none focus:ring-2 focus:ring-ring"
            />
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
              {{ editingExpenseId ? 'Speichern' : 'Hinzufügen' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <AppConfirmDialog
      v-model="showDeleteDialog"
      title="Ausgabe löschen?"
      :message="`Möchtest du die Ausgabe '${selectedExpense?.title || ''}' wirklich löschen?`"
      confirm-text="Löschen"
      cancel-text="Abbrechen"
      @confirm="confirmDeleteExpense"
    />
  </div>
</template>

<script setup>
import AppConfirmDialog from '@/components/AppConfirmDialog.vue'
import { ref } from 'vue'
import {
  Plus,
  Pencil,
  Trash2,
} from 'lucide-vue-next'

defineProps({
  expenses: {
    type: Array,
    required: true,
  },
  members: {
    type: Array,
    default: () => [],
  },
})

const emit = defineEmits([
  'addExpense',
  'updateExpense',
  'deleteExpense',
])

const categories = [
  'Miete',
  'Strom',
  'Internet',
  'Lebensmittel',
  'Haushalt',
  'Sonstiges',
]

const showDialog = ref(false)
const editingExpenseId = ref(null)
const showDeleteDialog = ref(false)
const selectedExpense = ref(null)
const formError = ref('')

const form = ref({
  title: '',
  amount: '',
  category: '',
  paidBy: '',
  date: new Date().toISOString().split('T')[0],
})

function openCreateDialog() {
  editingExpenseId.value = null
  formError.value = ''

  form.value = {
    title: '',
    amount: '',
    category: '',
    paidBy: '',
    date: new Date().toISOString().split('T')[0],
  }

  showDialog.value = true
}

function openEditDialog(expense) {
  editingExpenseId.value = expense.id
  formError.value = ''

  form.value = {
    title: expense.title,
    amount: expense.amount,
    category: expense.category,
    paidBy: expense.paidBy,
    date: expense.date,
  }

  showDialog.value = true
}

function closeDialog() {
  showDialog.value = false
  editingExpenseId.value = null
  formError.value = ''
}

function handleSubmit() {
  formError.value = ''

  if (!form.value.title.trim()) {
    formError.value = 'Bitte gib einen Titel ein.'
    return
  }

  if (!form.value.amount || Number(form.value.amount) <= 0) {
    formError.value = 'Bitte gib einen gültigen Betrag ein.'
    return
  }

  if (!form.value.category) {
    formError.value = 'Bitte wähle eine Kategorie aus.'
    return
  }

  if (!form.value.paidBy) {
    formError.value = 'Bitte wähle aus, wer bezahlt hat.'
    return
  }

  if (!form.value.date) {
    formError.value = 'Bitte wähle ein Datum aus.'
    return
  }

  const payload = {
    title: form.value.title.trim(),
    amount: parseFloat(form.value.amount),
    category: form.value.category,
    paidBy: form.value.paidBy,
    date: form.value.date,
  }

  if (editingExpenseId.value) {
    emit('updateExpense', editingExpenseId.value, payload)
  } else {
    emit('addExpense', payload)
  }

  closeDialog()
}

function deleteExpense(expense) {
  selectedExpense.value = expense
  showDeleteDialog.value = true
}

function confirmDeleteExpense() {
  if (!selectedExpense.value) {
    return
  }

  emit('deleteExpense', selectedExpense.value.id)
  selectedExpense.value = null
}

function getCategoryColor(category) {
  switch (String(category || '').toLowerCase()) {
    case 'miete':
      return 'bg-primary/10 text-primary'
    case 'strom':
      return 'bg-warning/20 text-warning-foreground'
    case 'internet':
      return 'bg-secondary text-secondary-foreground'
    case 'lebensmittel':
      return 'bg-accent/50 text-accent-foreground'
    case 'haushalt':
      return 'bg-accent/50 text-accent-foreground'
    default:
      return 'bg-purple/15 text-purple'
  }
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('de-DE')
}
</script>