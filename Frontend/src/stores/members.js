import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '@/lib/api'

export const useMembersStore = defineStore('members', () => {
  const members = ref([])
  const isLoading = ref(false)
  const error = ref(null)
  const pendingInvitationsCount = ref(0)

  async function removeMember(id) {
    await api.delete(`/members/${id}`)

    members.value = members.value.filter((member) => member.id !== id)
  }

  async function loadMembers() {
    isLoading.value = true
    error.value = null

    try {
      const response = await api.get('/members')
      members.value = response.data
    } catch (err) {
      error.value = 'Mitglieder konnten nicht geladen werden.'
      console.error(err)
    } finally {
      isLoading.value = false
    }
  }
  async function loadPendingInvitationsCount() {
  const response = await api.get('/invitations/pending-count')
  pendingInvitationsCount.value = response.data.count
}

  return {
    members,
    isLoading,
    error,
    pendingInvitationsCount,
    loadMembers,
    removeMember,
    loadPendingInvitationsCount
  }
})