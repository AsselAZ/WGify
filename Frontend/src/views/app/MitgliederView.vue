<template>
  <div class="min-h-screen">
    <AppNavbar
      title="Mitglieder"
      subtitle="Verwalte deine WG-Mitglieder"
      :show-search="false"
    />

    <div class="p-4 md:p-6 space-y-6">
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <DashboardCard
          title="Mitglieder"
          :value="members.length"
          subtitle="In der WG"
          :icon="Users"
        />

        <DashboardCard
          title="Admins"
          :value="adminCount"
          subtitle="Mit Adminrechten"
          :icon="Crown"
        />

        <DashboardCard
          title="Mitglieder"
          :value="regularCount"
          subtitle="Reguläre Mitglieder"
          :icon="UserCheck"
        />

        <DashboardCard
          title="Einladungen"
          :value="0"
          subtitle="Ausstehend"
          :icon="Mail"
        />
      </div>

      <div class="rounded-xl border border-border bg-card p-6">
        <p v-if="membersStore.isLoading" class="text-sm text-muted-foreground">
          Mitglieder werden geladen...
        </p>

        <p v-else-if="membersStore.error" class="text-sm text-red-500">
          {{ membersStore.error }}
        </p>

        <MemberList
          :members="members"
          @remove-member="membersStore.removeMember"
          v-else
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import {
  Users,
  Crown,
  UserCheck,
  Mail,
} from 'lucide-vue-next'

import AppNavbar from '@/components/AppNavbar.vue'
import DashboardCard from '@/components/DashboardCard.vue'
import MemberList from '@/components/MemberList.vue'
import { useMembersStore } from '@/stores/members'

const membersStore = useMembersStore()

onMounted(() => {
  membersStore.loadMembers()
})

const members = computed(() => membersStore.members)

const adminCount = computed(() => {
  return members.value.filter(member => member.role === 'admin').length
})

const regularCount = computed(() => {
  return members.value.filter(member => member.role !== 'admin').length
})

</script>