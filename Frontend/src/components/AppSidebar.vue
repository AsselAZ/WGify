<template>
  <button
    type="button"
    class="fixed left-0 top-4 z-50 flex h-10 w-10 items-center justify-center rounded-md border border-border bg-card text-foreground shadow-sm transition-colors hover:bg-muted lg:hidden"
    aria-label="Navigation öffnen"
    @click="mobileOpen = !mobileOpen"
  >
    <X
      v-if="mobileOpen"
      class="h-5 w-5"
    />
    <Menu
      v-else
      class="h-5 w-5"
    />
  </button>

  <div
    v-if="mobileOpen"
    class="fixed inset-0 z-30 bg-black/50 lg:hidden"
    @click="mobileOpen = false"
  />

  <aside
    :class="[
      'fixed inset-y-0 left-0 z-40 w-[280px] max-w-[85vw] transform border-r border-sidebar-border bg-sidebar transition-transform duration-200 ease-in-out lg:w-64 lg:translate-x-0',
      mobileOpen ? 'translate-x-0' : '-translate-x-full',
    ]"
  >
    <div class="flex h-full flex-col overflow-hidden">
      <div class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border px-6">
        <RouterLink
          to="/app/dashboard"
          class="inline-flex items-center"
          @click="mobileOpen = false"
        >
          <div class="logo-wrapper">
            <img
              src="/logo.png"
              alt="WGify Logo"
            />
          </div>
        </RouterLink>
      </div>

      <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <RouterLink
          v-for="item in navigation"
          :key="item.name"
          :to="item.href"
          :class="[
            'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
            isActive(item.href)
              ? 'bg-sidebar-accent text-sidebar-primary'
              : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground',
          ]"
          @click="mobileOpen = false"
        >
          <component
            :is="item.icon"
            class="h-5 w-5 shrink-0"
          />

          <span class="truncate">
            {{ item.name }}
          </span>
        </RouterLink>
      </nav>

      <div class="shrink-0 border-t border-sidebar-border p-4">
        <div class="flex items-center gap-3">
          <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple text-sm font-medium text-purple-foreground">
            {{ currentUserAvatar }}
          </div>

          <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-medium text-sidebar-foreground">
              {{ currentUserName }}
            </p>

            <p class="truncate text-xs text-sidebar-foreground/60">
              {{ currentUserRole }}
            </p>
          </div>

          <button
            type="button"
            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-md text-sidebar-foreground/70 transition-colors hover:bg-sidebar-accent hover:text-sidebar-foreground"
            aria-label="Abmelden"
            @click="handleLogout"
          >
            <LogOut class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>
  </aside>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import {
  RouterLink,
  useRoute,
  useRouter,
} from 'vue-router'
import {
  LayoutDashboard,
  Receipt,
  ListTodo,
  Users,
  Settings,
  Menu,
  X,
  LogOut,
} from 'lucide-vue-next'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const mobileOpen = ref(false)

const navigation = [
  {
    name: 'Dashboard',
    href: '/app/dashboard',
    icon: LayoutDashboard,
  },
  {
    name: 'Ausgaben',
    href: '/app/ausgaben',
    icon: Receipt,
  },
  {
    name: 'Aufgaben',
    href: '/app/aufgaben',
    icon: ListTodo,
  },
  {
    name: 'Mitglieder',
    href: '/app/mitglieder',
    icon: Users,
  },
  {
    name: 'Einstellungen',
    href: '/app/einstellungen',
    icon: Settings,
  },
]

onMounted(() => {
  authStore.loadUser()
})

const currentUserName = computed(() => {
  return authStore.currentUser?.name ?? 'Gast'
})

const currentUserAvatar = computed(() => {
  const name = authStore.currentUser?.name ?? 'Gast'
  return name.charAt(0).toUpperCase()
})

const currentUserRole = computed(() => {
  return authStore.currentUser?.role === 'admin' ? 'Admin' : 'Mitglied'
})

function isActive(href) {
  return route.path === href || route.path.startsWith(`${href}/`)
}

function handleLogout() {
  mobileOpen.value = false
  authStore.logout()
  router.push('/')
}
</script>

<style scoped>
.logo-wrapper {
  width: 50px;
  height: 50px;
  padding: 3px;
  border-radius: 12px;
  background-color: #ffffff;
}

.logo-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: contain;
}
</style>