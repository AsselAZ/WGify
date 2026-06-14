import { defineStore } from 'pinia'
import { ref } from 'vue'
import { members as initialMembers, type Member } from '@/lib/mockData'

export const useMembersStore = defineStore('members', () => {
  const members = ref<Member[]>([...initialMembers])

  function addMember(member: Omit<Member, 'id' | 'avatar'>) {
    members.value.push({
      ...member,
      id: Date.now().toString(),
      avatar: member.name.charAt(0).toUpperCase(),
    })
  }

  return { members, addMember }
})
