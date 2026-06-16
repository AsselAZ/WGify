import { defineStore } from 'pinia'
import { ref } from 'vue'
import { members as initialMembers, type Member } from '@/lib/mockData'

export const useMembersStore = defineStore('members', () => {
  const members = ref<Member[]>([])

  function normalizeEmail(email: string) {
    return email.trim().toLowerCase()
  }

  function removeDuplicateMembers(users: Member[]) {
    const seenEmails = new Set<string>()

    return users.filter(user => {
      const email = normalizeEmail(user.email)

      if (seenEmails.has(email)) {
        return false
      }

      seenEmails.add(email)
      return true
    })
  }

  function loadMembers() {
    const defaultMembers = initialMembers.filter(
      member => normalizeEmail(member.email) !== 'max@wgify.de'
    )

    const savedRegisteredUsers = localStorage.getItem('registeredUsers')
    const registeredUsers: Member[] = savedRegisteredUsers
      ? JSON.parse(savedRegisteredUsers)
      : []

    const savedCurrentUser = localStorage.getItem('currentUser')
    const currentUser: Member[] = savedCurrentUser
      ? [JSON.parse(savedCurrentUser)]
      : []

    const allMembers = [
      ...defaultMembers,
      ...registeredUsers,
      ...currentUser,
    ]

    members.value = removeDuplicateMembers(allMembers)
  }

  function addMember(member: Omit<Member, 'id' | 'avatar'>) {
    const newMember: Member = {
      ...member,
      id: Date.now().toString(),
      avatar: member.name.charAt(0).toUpperCase(),
    }

    const savedRegisteredUsers = localStorage.getItem('registeredUsers')
    const registeredUsers: Member[] = savedRegisteredUsers
      ? JSON.parse(savedRegisteredUsers)
      : []

    const updatedRegisteredUsers = removeDuplicateMembers([
      ...registeredUsers,
      newMember,
    ])

    localStorage.setItem('registeredUsers', JSON.stringify(updatedRegisteredUsers))

    loadMembers()
  }

  loadMembers()

  return {
    members,
    loadMembers,
    addMember,
  }
})