<template>
  <div class="space-y-4">
    <div class="flex items-center justify-between">
      <h2 class="text-lg font-semibold">Alle Ausgaben</h2>
      <button
        class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors"
        @click="showDialog = true"
      >
        <Plus class="h-4 w-4" />
        Neue Ausgabe
      </button>
    </div>

    <!-- Table -->
    <div class="rounded-xl border border-border overflow-hidden">
      <table class="w-full text-sm">
        <thead>
          <tr class="bg-secondary/40">
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Titel</th>
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Kategorie</th>
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Bezahlt von</th>
            <th class="text-left px-4 py-3 font-medium text-muted-foreground">Datum</th>
            <th class="text-right px-4 py-3 font-medium text-muted-foreground">Betrag</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="expense in expenses"
            :key="expense.id"
            class="border-t border-border hover:bg-muted/30 transition-colors"
          >
            <td class="px-4 py-3 font-medium">{{ expense.title }}</td>
            <td class="px-4 py-3">
              <span :class="['inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium', getCategoryColor(expense.category)]">
                {{ expense.category }}
              </span>
            </td>
            <td class="px-4 py-3">{{ expense.paidBy }}</td>
            <td class="px-4 py-3 text-muted-foreground">{{ formatDate(expense.date) }}</td>
            <td class="px-4 py-3 text-right font-semibold">{{ expense.amount.toFixed(2) }} EUR</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Dialog -->
    <div v-if="showDialog" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div class="bg-card rounded-xl border border-border p-6 w-full max-w-md mx-4 shadow-xl">
        <h3 class="text-lg font-semibold mb-4">Neue Ausgabe hinzufügen</h3>
        <form class="space-y-4" @submit.prevent="handleSubmit">
          <div class="space-y-2">
            <label class="text-sm font-medium">Titel</label>
            <input v-model="form.title" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" placeholder="z.B. Internet" />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Betrag (EUR)</label>
            <input v-model="form.amount" type="number" step="0.01" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" placeholder="0.00" />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Kategorie</label>
            <select v-model="form.category" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring">
              <option value="">Kategorie wählen</option>
              <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Bezahlt von</label>
            <select v-model="form.paidBy" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring">
              <option value="">Person wählen</option>
              <option v-for="m in members" :key="m.id" :value="m.name">{{ m.name }}</option>
            </select>
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Datum</label>
            <input v-model="form.date" type="date" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
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

<script setup lang="ts">
import { ref } from 'vue'
import { Plus } from 'lucide-vue-next'
import { categories, members, type Expense } from '@/lib/mockData'

const props = defineProps<{ expenses: Expense[] }>()
const emit = defineEmits<{ addExpense: [expense: Omit<Expense, 'id'>] }>()

const showDialog = ref(false)
const form = ref({ title: '', amount: '', category: '', paidBy: '', date: new Date().toISOString().split('T')[0] })

function handleSubmit() {
  if (!form.value.title || !form.value.amount || !form.value.category || !form.value.paidBy) return
  emit('addExpense', {
    title: form.value.title,
    amount: parseFloat(form.value.amount),
    category: form.value.category,
    paidBy: form.value.paidBy,
    date: form.value.date,
  })
  form.value = { title: '', amount: '', category: '', paidBy: '', date: new Date().toISOString().split('T')[0] }
  showDialog.value = false
}

function getCategoryColor(category: string) {
  switch (category.toLowerCase()) {
    case 'miete': return 'bg-primary/10 text-primary'
    case 'strom': return 'bg-warning/20 text-warning-foreground'
    case 'internet': return 'bg-secondary text-secondary-foreground'
    case 'lebensmittel': return 'bg-accent/50 text-accent-foreground'
    case 'haushalt': return 'bg-accent/50 text-accent-foreground'
    default: return 'bg-purple/15 text-purple'
  }
}

function formatDate(date: string) {
  return new Date(date).toLocaleDateString('de-DE')
}
</script>
