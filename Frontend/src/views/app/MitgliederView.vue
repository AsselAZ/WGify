<template>
  <div class="flex min-h-screen w-full flex-col">
    <AppNavbar
      title="Mitglieder"
      subtitle="Verwalte deine WG-Mitglieder"
      :show-search="false"
    />

    <div class="space-y-6 p-4 sm:p-6">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 2xl:grid-cols-4">
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
          title="Regulär"
          :value="regularCount"
          subtitle="Reguläre Mitglieder"
          :icon="UserCheck"
        />

        <DashboardCard
          title="Einladungen"
          :value="membersStore.pendingInvitationsCount"
          subtitle="Ausstehend"
          :icon="Mail"
        />
      </div>

      <div class="rounded-xl border border-border bg-card p-4 sm:p-6">
        <p
          v-if="membersStore.isLoading"
          class="text-sm text-muted-foreground"
        >
          Mitglieder werden geladen...
        </p>

        <p
          v-else-if="membersStore.error"
          class="text-sm text-red-500"
        >
          {{ membersStore.error }}
        </p>

        <MemberList
          v-else
          :members="members"
          @remove-member="membersStore.removeMember"
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
  membersStore.loadPendingInvitationsCount()
})

const members = computed(() => membersStore.members)

const adminCount = computed(() => {
  return members.value.filter((member) => member.role === 'admin').length
})

const regularCount = computed(() => {
  return members.value.filter((member) => member.role !== 'admin').length
})
</script>