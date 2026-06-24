<template>
  <div class="min-h-screen">
    <AppNavbar
  	title="Mitglieder"
  	subtitle="Verwalte deine WG-Mitglieder"
  	:show-search="false"
     />
    <div class="p-4 md:p-6 space-y-6">
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <DashboardCard title="Mitglieder" :value="store.members.length" subtitle="In der WG" :icon="Users" />
        <DashboardCard title="Admins" :value="adminCount" subtitle="Mit Adminrechten" :icon="Crown" />
        <DashboardCard title="Mitglieder" :value="regularCount" subtitle="Reguläre Mitglieder" :icon="UserCheck" />
        <DashboardCard title="Einladungen" :value="0" subtitle="Ausstehend" :icon="Mail" />
      </div>
      <div class="rounded-xl border border-border bg-card p-6">
        <MemberList :members="store.members" @add-member="store.addMember" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Users, Crown, UserCheck, Mail } from 'lucide-vue-next'
import AppNavbar from '@/components/AppNavbar.vue'
import DashboardCard from '@/components/DashboardCard.vue'
import MemberList from '@/components/MemberList.vue'
import { useMembersStore } from '@/stores/members'

const store = useMembersStore()
const adminCount = computed(() => store.members.filter(m => m.role === 'admin').length)
const regularCount = computed(() => store.members.filter(m => m.role === 'mitglied').length)
</script>
