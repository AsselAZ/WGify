<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">Alle Ausgaben</h2>

      <button
        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors"
        @click="openCreateDialog"
      >
        <Plus class="h-4 w-4" />
        Neue Ausgabe
      </button>
    </div>

    <div class="rounded-xl border border-border overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-secondary/40">
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Titel</th>
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Kategorie</th>
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Bezahlt von</th>
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Datum</th>
            <th class="text-right px-4 py-3 font-medium text-muted-foreground">Betrag</th>
            <th class="text-right px-4 py-3 font-medium text-muted-foreground">Aktionen</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="expense in expenses"
            :key="expense.id"
            class="border-t border-border hover:bg-muted/30 transition-colors"
          >
            <td class="px-4 py-3 font-medium">
              {{ expense.title }}
            </td>

            <td class="px-4 py-3">
              <span
                :class="[
                  'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                  getCategoryColor(expense.category)
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
                  class="inline-flex items-center gap-1 rounded-md border border-border px-2 py-1 text-xs hover:bg-muted transition-colors"
                  @click="openEditDialog(expense)"
                >
                  <Pencil class="h-3.5 w-3.5" />
                  Bearbeiten
                </button>

                <button
                  type="button"
                  class="inline-flex items-center gap-1 rounded-md border border-red-200 px-2 py-1 text-xs text-red-600 hover:bg-red-50 transition-colors"
                  @click="deleteExpense(expense)"
                >
                  <Trash2 class="h-3.5 w-3.5" />
                  Löschen
                </button>
              </div>
            </td>
          </tr>

          <tr v-if="expenses.length === 0">
            <td colspan="6" class="px-4 py-8 text-center text-muted-foreground">
              Noch keine Ausgaben vorhanden.
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
          {{ editingExpenseId ? 'Ausgabe bearbeiten' : 'Neue Ausgabe hinzufügen' }}
        </h3>
        

        <form class="space-y-4" @submit.prevent="handleSubmit" novalidate>
          <div class="space-y-2">
            <label class="text-sm font-medium">Titel</label>
            <input
              v-model="form.title"
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
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
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              placeholder="0.00"
              
            />
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Kategorie</label>
            <select
              v-model="form.category"
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              
            >
              <option value="">Kategorie wählen</option>
              <option v-for="cat in categories" :key="cat" :value="cat">
                {{ cat }}
              </option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-sm font-medium">Bezahlt von</label>
            <select
              v-model="form.paidBy"
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
            <label class="text-sm font-medium">Datum</label>
            <input
              v-model="form.date"
              type="date"
              class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring"
              
            />
          </div>
 <p v-if="formError" class="mb-3 rounded-md bg-red-50 px-3 py-2 text-sm text-red-600">
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
              {{ editingExpenseId ? 'Speichern' : 'Hinzufügen' }}
            </button>
          </div>
        </form>
      </div>
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
    title: form.value.title,
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
  switch (category.toLowerCase()) {
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