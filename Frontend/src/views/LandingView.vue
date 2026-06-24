<template>
  <div class="min-h-screen bg-background">
    <!-- Header -->
    <header class="border-b border-border bg-card/80 backdrop-blur-sm sticky top-0 z-50">
      <div class="container mx-auto px-4 h-16 flex items-center justify-between">
        <!-- Logo -->
        <WGLogo :width="50" :height="50" margin="0"/>
        <!--
        <span class="text-xl font-black">
          <span class="text-primary">WG</span><span class="text-purple">ify</span>
        </span>
        -->
        <div class="flex items-center gap-3">
          <RouterLink to="/login">
            <button class="px-4 py-2 rounded-md text-sm font-medium hover:bg-accent transition-colors">Anmelden</button>
          </RouterLink>
          <RouterLink to="/registrieren">
            <button class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-primary text-primary-foreground text-sm font-medium hover:bg-primary/90 transition-colors">
              Registrieren
              <ArrowRight class="h-4 w-4" />
            </button>
          </RouterLink>
        </div>
      </div>
    </header>

    <!-- Hero -->
    <section class="py-20 md:py-32 relative overflow-hidden">
      <div class="absolute inset-0 bg-gradient-to-br from-secondary/40 via-transparent to-accent/30 pointer-events-none" />
      <div class="container mx-auto px-4 text-center relative">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-purple/15 text-foreground text-sm font-medium mb-6">
          <Sparkles class="h-4 w-4 text-purple" />
          Kostenlos starten
        </div>
        <h1 class="text-4xl md:text-6xl font-bold mb-6">
          Deine WG. <span class="text-purple">Einfach organisiert.</span>
        </h1>
        <p class="text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto mb-8">
          WGify hilft dir und deinen Mitbewohnern, Ausgaben zu teilen, Aufgaben zu verwalten und das Zusammenleben zu vereinfachen.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
          <RouterLink to="/registrieren">
            <button class="inline-flex items-center gap-2 px-6 py-3 rounded-md bg-primary text-primary-foreground font-medium hover:bg-primary/90 transition-colors">
              Kostenlos starten <ArrowRight class="h-4 w-4" />
            </button>
          </RouterLink>
          <button class="px-6 py-3 rounded-md border border-border font-medium hover:bg-muted transition-colors" @click="scrollToSection">
            Mehr erfahren
          </button>
        </div>
      </div>
    </section>

    <!-- Features -->
    <section class="py-20 border-t border-border bg-muted/30">
      <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center mb-4">Alles was du brauchst</h2>
        <p class="text-muted-foreground text-center max-w-xl mx-auto mb-12">
          WGify bietet alle Funktionen, die du und deine Mitbewohner für ein harmonisches Zusammenleben braucht.
        </p>
        <div class="grid md:grid-cols-3 gap-8">
          <div class="rounded-xl border border-border bg-card p-8 text-center hover:shadow-lg transition-shadow">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-purple/20 mx-auto mb-6">
              <Receipt class="h-7 w-7 text-purple" />
            </div>
            <h3 class="text-xl font-semibold mb-3">Ausgaben verwalten</h3>
            <p class="text-muted-foreground">Erfasse und teile alle WG-Ausgaben fair auf. Behalte den Überblick über wer wem was schuldet.</p>
          </div>
          <div class="rounded-xl border border-border bg-card p-8 text-center hover:shadow-lg transition-shadow">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-accent/50 mx-auto mb-6">
              <ListTodo class="h-7 w-7 text-primary" />
            </div>
            <h3 class="text-xl font-semibold mb-3">Aufgaben planen</h3>
            <p class="text-muted-foreground">Erstelle Putzpläne, verteile Aufgaben und sorge dafür, dass nichts vergessen wird.</p>
          </div>
          <div class="rounded-xl border border-border bg-card p-8 text-center hover:shadow-lg transition-shadow">
            <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-secondary mx-auto mb-6">
              <Users class="h-7 w-7 text-primary" />
            </div>
            <h3 class="text-xl font-semibold mb-3">Team verwalten</h3>
            <p class="text-muted-foreground">Lade Mitbewohner ein, verwalte Rollen und halte alle auf dem Laufenden.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Benefits -->
    <section class="py-50 border-t border-border" id="MehrErfahrenZiel">
      <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 gap-12 items-center">
          <div>
            <!-- Logo 
            <WGLogo :width="100" :height="100" margin="0" display="block"/>
            -->
            
            <h2 class="text-3xl font-bold mb-6">
              Warum <span class="text-primary">WG</span><span class="text-purple">ify</span>?
            </h2>
            
            <ul class="space-y-4">
              <li v-for="benefit in benefits" :key="benefit.title" class="flex items-start gap-3">
                <CheckCircle class="h-6 w-6 text-accent flex-shrink-0 mt-0.5" />
                <div>
                  <p class="font-medium">{{ benefit.title }}</p>
                  <p class="text-muted-foreground">{{ benefit.desc }}</p>
                </div>
              </li>
            </ul>
          </div>
          <div class="rounded-xl border border-border bg-card p-8 shadow-lg">
            <div class="space-y-4">
              <div v-for="(item, i) in previewItems" :key="i" :class="['flex items-center justify-between p-4 rounded-lg', item.bg]">
                <div class="flex items-center gap-3">
                  <div :class="['h-10 w-10 rounded-full flex items-center justify-center font-medium', item.avatarBg]">{{ item.avatar }}</div>
                  <div>
                    <p class="font-medium">{{ item.name }}</p>
                    <p class="text-sm text-muted-foreground">{{ item.desc }}</p>
                  </div>
                </div>
                <p class="font-semibold">{{ item.amount }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA -->
    <section class="py-20 border-t border-border bg-primary text-primary-foreground">
      <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Bereit für ein organisiertes WG-Leben?</h2>
        <p class="text-primary-foreground/80 mb-8 max-w-xl mx-auto">
          Starte jetzt kostenlos und erlebe, wie einfach das Zusammenleben sein kann.
        </p>
        <RouterLink to="/registrieren">
          <button class="inline-flex items-center gap-2 px-6 py-3 rounded-md bg-secondary text-secondary-foreground font-medium hover:bg-secondary/80 transition-colors">
            Jetzt loslegen <ArrowRight class="h-4 w-4" />
          </button>
        </RouterLink>
      </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-border py-8 bg-card">
      <div class="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Logo -->
        <WGLogo :width="70" :height="70" margin="0"/>
        <!--
        <span class="text-lg font-black">
          <span class="text-primary">WG</span><span class="text-purple">ify</span>
        </span>
        -->
        <p class="text-sm text-muted-foreground">2024 WGify. Alle Rechte vorbehalten.</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router'
import { ArrowRight, Sparkles, Receipt, ListTodo, Users, CheckCircle } from 'lucide-vue-next'
import WGLogo from '@/components/WGifyLogo.vue'

const benefits = [
  { title: 'Einfach zu bedienen', desc: 'Intuitive Oberfläche, keine Einarbeitung nötig.' },
  { title: 'Immer synchron', desc: 'Änderungen werden sofort für alle sichtbar.' },
  { title: 'Kostenlos nutzbar', desc: 'Alle Grundfunktionen ohne Kosten verfügbar.' },
  { title: 'Responsive Design', desc: 'Funktioniert auf Desktop, Tablet und Smartphone.' },
]

const previewItems = [
  { bg: 'bg-secondary/60', avatar: 'A', avatarBg: 'bg-primary text-primary-foreground', name: 'Assel', desc: 'Internet bezahlt', amount: '39,99 EUR' },
  { bg: 'bg-accent/40', avatar: 'M', avatarBg: 'bg-accent text-accent-foreground', name: 'Max', desc: 'Strom bezahlt', amount: '85,50 EUR' },
  { bg: 'bg-purple/20', avatar: 'L', avatarBg: 'bg-purple text-purple-foreground', name: 'Lisa', desc: 'Einkauf', amount: '67,30 EUR' },
]

const scrollToSection = () => {
  document.getElementById('MehrErfahrenZiel')?.scrollIntoView({
    behavior: 'smooth'
  })
}

</script>
