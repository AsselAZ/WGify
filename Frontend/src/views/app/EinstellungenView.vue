<template>
  <div class="min-h-screen">
    <AppNavbar title="Einstellungen" subtitle="Verwalte dein Profil und die WG" />
    <div class="p-4 md:p-6 space-y-6 max-w-3xl">

      <!-- Profil -->
      <div class="rounded-xl border border-border bg-card p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary">
            <User class="h-5 w-5 text-primary" />
          </div>
          <div>
            <h2 class="text-lg font-semibold">Profil</h2>
            <p class="text-sm text-muted-foreground">Persönliche Informationen bearbeiten</p>
          </div>
        </div>
        <div class="space-y-4">
          <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary text-primary-foreground text-2xl font-semibold">A</div>
            <button class="px-4 py-2 rounded-md border border-border text-sm font-medium hover:bg-muted transition-colors">Bild ändern</button>
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
              <label class="text-sm font-medium">Name</label>
              <input v-model="profile.name" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium">E-Mail</label>
              <input v-model="profile.email" type="email" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
            </div>
          </div>
        </div>
      </div>

      <!-- Passwort -->
      <div class="rounded-xl border border-border bg-card p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple/15">
            <Lock class="h-5 w-5 text-purple" />
          </div>
          <div>
            <h2 class="text-lg font-semibold">Passwort</h2>
            <p class="text-sm text-muted-foreground">Passwort ändern</p>
          </div>
        </div>
        <div class="space-y-4">
          <div class="space-y-2">
            <label class="text-sm font-medium">Aktuelles Passwort</label>
            <input type="password" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
          </div>
          <div class="grid gap-4 sm:grid-cols-2">
            <div class="space-y-2">
              <label class="text-sm font-medium">Neues Passwort</label>
              <input type="password" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
            </div>
            <div class="space-y-2">
              <label class="text-sm font-medium">Passwort bestätigen</label>
              <input type="password" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
            </div>
          </div>
          <button class="px-4 py-2 rounded-md border border-border text-sm font-medium hover:bg-muted transition-colors">Passwort ändern</button>
        </div>
      </div>

      <!-- WG-Einstellungen -->
      <div class="rounded-xl border border-border bg-card p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent/50">
            <Home class="h-5 w-5 text-primary" />
          </div>
          <div>
            <h2 class="text-lg font-semibold">WG-Einstellungen</h2>
            <p class="text-sm text-muted-foreground">Allgemeine WG-Informationen</p>
          </div>
        </div>
        <div class="space-y-4">
          <div class="space-y-2">
            <label class="text-sm font-medium">WG-Name</label>
            <input v-model="wg.name" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Adresse</label>
            <input v-model="wg.address" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring" />
          </div>
          <div class="space-y-2">
            <label class="text-sm font-medium">Währung</label>
            <select v-model="wg.currency" class="w-full px-3 h-9 rounded-md border border-border bg-input text-sm outline-none focus:ring-2 focus:ring-ring">
              <option value="EUR">Euro (EUR)</option>
              <option value="USD">US Dollar ($)</option>
              <option value="CHF">Schweizer Franken (CHF)</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Benachrichtigungen -->
      <div class="rounded-xl border border-border bg-card p-6">
        <div class="flex items-center gap-3 mb-6">
          <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning/20">
            <Bell class="h-5 w-5 text-warning-foreground" />
          </div>
          <div>
            <h2 class="text-lg font-semibold">Benachrichtigungen</h2>
            <p class="text-sm text-muted-foreground">Benachrichtigungseinstellungen verwalten</p>
          </div>
        </div>
        <div class="space-y-4 divide-y divide-border">
          <div v-for="item in notifItems" :key="item.key" class="flex items-center justify-between py-3 first:pt-0 last:pb-0">
            <div>
              <p class="font-medium">{{ item.label }}</p>
              <p class="text-sm text-muted-foreground">{{ item.desc }}</p>
            </div>
            <button
              :class="['relative w-11 h-6 rounded-full transition-colors', notifs[item.key] ? 'bg-primary' : 'bg-muted']"
              @click="notifs[item.key] = !notifs[item.key]"
            >
              <span :class="['absolute top-0.5 w-5 h-5 rounded-full bg-white shadow transition-transform', notifs[item.key] ? 'translate-x-5' : 'translate-x-0.5']" />
            </button>
          </div>
        </div>
      </div>

      <!-- Save -->
      <div class="flex justify-end">
        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors">
          <Save class="h-4 w-4" />
          Änderungen speichern
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue'
import { User, Lock, Home, Bell, Save } from 'lucide-vue-next'
import AppNavbar from '@/components/AppNavbar.vue'

const profile = reactive({ name: 'Assel', email: 'assel@wgify.de' })
const wg = reactive({ name: 'Muster-WG', address: 'Musterstrasse 123, 12345 Berlin', currency: 'EUR' })
const notifs = reactive({ email: true, push: false, tasks: true, expenses: true })
const notifItems = [
  { key: 'email' as const, label: 'E-Mail Benachrichtigungen', desc: 'Erhalte Updates per E-Mail' },
  { key: 'push' as const, label: 'Push Benachrichtigungen', desc: 'Browser Push-Nachrichten' },
  { key: 'tasks' as const, label: 'Aufgaben-Erinnerungen', desc: 'Erinnerungen für anstehende Aufgaben' },
  { key: 'expenses' as const, label: 'Ausgaben-Updates', desc: 'Benachrichtigungen bei neuen Ausgaben' },
]
</script>
