<template>
  <div class="min-h-screen bg-background">
    <!-- Header -->
    <header class="sticky top-0 z-50 border-b border-border bg-card/80 backdrop-blur-sm">
      <div class="mx-auto flex h-16 w-full max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <WGLogo
          :width="50"
          :height="50"
          margin="0"
        />

        <div class="flex items-center gap-2 sm:gap-3">
          <RouterLink to="/login">
            <button
              type="button"
              class="rounded-md px-3 py-2 text-sm font-medium transition-colors hover:bg-accent sm:px-4"
            >
              Anmelden
            </button>
          </RouterLink>

          <RouterLink to="/registrieren">
            <button
              type="button"
              class="inline-flex items-center gap-2 rounded-md bg-primary px-3 py-2 text-sm font-medium text-primary-foreground transition-colors hover:bg-primary/90 sm:px-4"
            >
              Registrieren
              <ArrowRight class="hidden h-4 w-4 sm:block" />
            </button>
          </RouterLink>
        </div>
      </div>
    </header>

    <!-- Hero -->
    <section class="relative overflow-hidden py-16 sm:py-20 md:py-28 lg:py-32">
      <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-secondary/40 via-transparent to-accent/30" />

      <div class="relative mx-auto w-full max-w-7xl px-4 text-center sm:px-6 lg:px-8">
        <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-purple/15 px-4 py-2 text-sm font-medium text-foreground">
          <Sparkles class="h-4 w-4 text-purple" />
          Kostenlos starten
        </div>

        <h1 class="mx-auto max-w-4xl text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">
          Deine WG.
          <span class="text-purple">
            Einfach organisiert.
          </span>
        </h1>

        <p class="mx-auto mt-6 max-w-2xl text-base text-muted-foreground sm:text-lg md:text-xl">
         <span class="text-primary">WG</span><span class="text-purple">ify</span> hilft dir und deinen Mitbewohnern, Ausgaben zu teilen, Aufgaben zu verwalten und das Zusammenleben zu vereinfachen.
        </p>

        <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row sm:gap-4">
          <RouterLink
            to="/registrieren"
            class="w-full sm:w-auto"
          >
            <button
              type="button"
              class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-primary px-6 py-3 font-medium text-primary-foreground transition-colors hover:bg-primary/90 sm:w-auto"
            >
              Kostenlos starten
              <ArrowRight class="h-4 w-4" />
            </button>
          </RouterLink>

          <button
            type="button"
            class="w-full rounded-md border border-border px-6 py-3 font-medium transition-colors hover:bg-muted sm:w-auto"
            @click="scrollToSection"
          >
            Mehr erfahren
          </button>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="border-t border-border bg-muted/30 py-16 sm:py-20">
      <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
          <h2 class="text-3xl font-bold">
            Alles was du brauchst
          </h2>

          <p class="mt-4 text-muted-foreground">
            <span class="text-primary">WG</span><span class="text-purple">ify</span> bietet alle Funktionen, die du und deine Mitbewohner für ein harmonisches Zusammenleben braucht.
          </p>
        </div>

        <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3 lg:gap-8">
          <div class="rounded-xl border border-border bg-card p-6 text-center transition-shadow hover:shadow-lg sm:p-8">
            <div class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-purple/20">
              <Receipt class="h-7 w-7 text-purple" />
            </div>

            <h3 class="mb-3 text-xl font-semibold">
              Ausgaben verwalten
            </h3>

            <p class="text-muted-foreground">
              Erfasse alle WG-Ausgaben und behalte somit den Überblick darüber, wer was ausgegeben hat.
            </p>
          </div>

          <div class="rounded-xl border border-border bg-card p-6 text-center transition-shadow hover:shadow-lg sm:p-8">
            <div class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-accent/50">
              <ListTodo class="h-7 w-7 text-primary" />
            </div>

            <h3 class="mb-3 text-xl font-semibold">
              Aufgaben planen
            </h3>

            <p class="text-muted-foreground">
              Erstelle Putzpläne, verteile Aufgaben und sorge dafür, dass nichts vergessen wird.
            </p>
          </div>

          <div class="rounded-xl border border-border bg-card p-6 text-center transition-shadow hover:shadow-lg sm:p-8">
            <div class="mx-auto mb-6 flex h-14 w-14 items-center justify-center rounded-xl bg-secondary">
              <Users class="h-7 w-7 text-primary" />
            </div>

            <h3 class="mb-3 text-xl font-semibold">
              Team verwalten
            </h3>

            <p class="text-muted-foreground">
              Lade Mitbewohner ein, verwalte Rollen und halte alle auf dem Laufenden.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Benefits -->
    <section
      id="MehrErfahrenZiel"
      class="border-t border-border py-16 sm:py-20"
    >
      <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-10 md:grid-cols-2 lg:gap-12">
          <div>
            <h2 class="mb-6 text-3xl font-bold">
              Warum
              <span class="text-primary">WG</span><span class="text-purple">ify</span>?
            </h2>

            <ul class="space-y-4">
              <li
                v-for="benefit in benefits"
                :key="benefit.title"
                class="flex items-start gap-3"
              >
                <CheckCircle class="mt-0.5 h-6 w-6 shrink-0 text-accent" />

                <div class="min-w-0">
                  <p class="font-medium">
                    {{ benefit.title }}
                  </p>

                  <p class="text-muted-foreground">
                    {{ benefit.desc }}
                  </p>
                </div>
              </li>
            </ul>
          </div>

          <div class="rounded-xl border border-border bg-card p-4 shadow-lg sm:p-6 lg:p-8">
            <div class="space-y-4">
              <div
                v-for="(item, i) in previewItems"
                :key="i"
                :class="[
                  'flex flex-col gap-3 rounded-lg p-4 sm:flex-row sm:items-center sm:justify-between',
                  item.bg,
                ]"
              >
                <div class="flex min-w-0 items-center gap-3">
                  <div
                    :class="[
                      'flex h-10 w-10 shrink-0 items-center justify-center rounded-full font-medium',
                      item.avatarBg,
                    ]"
                  >
                    {{ item.avatar }}
                  </div>

                  <div class="min-w-0">
                    <p class="truncate font-medium">
                      {{ item.name }}
                    </p>

                    <p class="truncate text-sm text-muted-foreground">
                      {{ item.desc }}
                    </p>
                  </div>
                </div>

                <p class="shrink-0 font-semibold sm:text-right">
                  {{ item.amount }}
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="border-t border-border bg-primary py-16 text-primary-foreground sm:py-20">
      <div class="mx-auto w-full max-w-7xl px-4 text-center sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold">
          Bereit für ein organisiertes WG-Leben?
        </h2>

        <p class="mx-auto mt-4 max-w-xl text-primary-foreground/80">
          Starte jetzt kostenlos und erlebe, wie einfach das Zusammenleben sein kann.
        </p>

        <RouterLink
          to="/registrieren"
          class="mt-8 inline-block w-full sm:w-auto"
        >
          <button
            type="button"
            class="inline-flex w-full items-center justify-center gap-2 rounded-md bg-secondary px-6 py-3 font-medium text-secondary-foreground transition-colors hover:bg-secondary/80 sm:w-auto"
          >
            Jetzt loslegen
            <ArrowRight class="h-4 w-4" />
          </button>
        </RouterLink>
      </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-border bg-card py-8">
      <div class="mx-auto flex w-full max-w-7xl flex-col items-center justify-between gap-4 px-4 text-center sm:px-6 md:flex-row md:text-left lg:px-8">
        <WGLogo
          :width="70"
          :height="70"
          margin="0"
        />

        <p class="text-sm text-muted-foreground">
          2026 WGify. Alle Rechte vorbehalten.
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'
import {
  ArrowRight,
  Sparkles,
  Receipt,
  ListTodo,
  Users,
  CheckCircle,
} from 'lucide-vue-next'
import WGLogo from '@/components/WGifyLogo.vue'

const benefits = [
  {
    title: 'Einfach zu bedienen',
    desc: 'Intuitive Oberfläche, keine Einarbeitung nötig.',
  },
  {
    title: 'Immer synchron',
    desc: 'Änderungen werden sofort für alle sichtbar.',
  },
  {
    title: 'Kostenlos nutzbar',
    desc: 'Alle Grundfunktionen ohne Kosten verfügbar.',
  },
  {
    title: 'Responsive Design',
    desc: 'Funktioniert auf Desktop, Tablet und Smartphone.',
  },
]

const previewItems = [
  {
    bg: 'bg-secondary/60',
    avatar: 'A',
    avatarBg: 'bg-primary text-primary-foreground',
    name: 'Assel',
    desc: 'Internet bezahlt',
    amount: '39,99 EUR',
  },
  {
    bg: 'bg-accent/40',
    avatar: 'M',
    avatarBg: 'bg-accent text-accent-foreground',
    name: 'Max',
    desc: 'Strom bezahlt',
    amount: '85,50 EUR',
  },
  {
    bg: 'bg-purple/20',
    avatar: 'L',
    avatarBg: 'bg-purple text-purple-foreground',
    name: 'Lisa',
    desc: 'Einkauf',
    amount: '67,30 EUR',
  },
]

function scrollToSection() {
  document.getElementById('MehrErfahrenZiel')?.scrollIntoView({
    behavior: 'smooth',
  })
}
</script>