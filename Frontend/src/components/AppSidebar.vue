<template>
  <!-- Mobile menu button -->
  <button
    class="fixed top-4 left-4 z-50 md:hidden flex items-center justify-center w-9 h-9 rounded-md hover:bg-gray-100 text-foreground"
    @click="mobileOpen = !mobileOpen"
  >
    <X v-if="mobileOpen" class="w-5 h-5" />
    <Menu v-else class="w-5 h-5" />
  </button>

  <!-- Mobile overlay -->
  <div
    v-if="mobileOpen"
    class="fixed inset-0 z-30 bg-black/50 md:hidden"
    @click="mobileOpen = false"
  />

  <!-- Sidebar -->
  <aside
    :class="[
      'fixed inset-y-0 left-0 z-40 w-64 bg-sidebar border-r border-sidebar-border transform transition-transform duration-200 ease-in-out md:translate-x-0',
      mobileOpen ? 'translate-x-0' : '-translate-x-full'
    ]"
  >
    <div class="flex h-full flex-col">
      <!-- Logo -->
      <div class="flex h-16 items-center gap-2 px-6 border-b border-sidebar-border">
        <div class="logo-wrapper">
          <img src="/logo.png" alt="WGify Logo" />
        </div>
      </div>

      <!-- Navigation -->
      <nav class="flex-1 space-y-1 px-3 py-4">
        <RouterLink
          v-for="item in navigation"
          :key="item.name"
          :to="item.href"
          @click="mobileOpen = false"
          :class="[
            'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
            route.path === item.href
              ? 'bg-sidebar-accent text-sidebar-primary'
              : 'text-sidebar-foreground/70 hover:bg-sidebar-accent hover:text-sidebar-foreground'
          ]"
        >
          <component :is="item.icon" class="h-5 w-5" />
          {{ item.name }}
        </RouterLink>
      </nav>

      <!-- Footer -->
      <div class="border-t border-sidebar-border p-4">
        <div class="flex items-center gap-3">
          <div
            class="flex h-9 w-9 items-center justify-center rounded-full bg-purple text-purple-foreground text-sm font-medium"
          >
            {{ currentUserAvatar }}
          </div>

          <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-sidebar-foreground truncate">
              {{ currentUserName }}
            </p>
            <p class="text-xs text-sidebar-foreground/60 truncate">
              {{ currentUserRole }}
            </p>
          </div>

          <button
            class="flex items-center justify-center w-9 h-9 rounded-md text-sidebar-foreground/70 hover:text-sidebar-foreground hover:bg-sidebar-accent transition-colors"
            @click="handleLogout"
          >
            <LogOut class="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>
  </aside>
</template>

<!-- Logo Style in Sidebar -->
<style scoped>
.logo-wrapper {
  background-color: #ffffff;
  padding: 3px;
  border-radius: 12px;
  width: 50px;
  height: 50px;
}
</style>

<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'
import { RouterLink, useRoute, useRouter } from 'vue-router'
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

onMounted(() => {
  authStore.loadUser()
})

const currentUserName = computed(() => authStore.currentUser?.name ?? 'Gast')
const currentUserAvatar = computed(() => authStore.currentUser?.avatar ?? 'G')

const currentUserRole = computed(() =>
  authStore.currentUser?.role === 'admin' ? 'Admin' : 'Mitglied'
)

function handleLogout() {
  authStore.logout()
  router.push('/')
}

const navigation = [
  { name: 'Dashboard', href: '/app/dashboard', icon: LayoutDashboard },
  { name: 'Ausgaben', href: '/app/ausgaben', icon: Receipt },
  { name: 'Aufgaben', href: '/app/aufgaben', icon: ListTodo },
  { name: 'Mitglieder', href: '/app/mitglieder', icon: Users },
  { name: 'Einstellungen', href: '/app/einstellungen', icon: Settings },
]
</script>