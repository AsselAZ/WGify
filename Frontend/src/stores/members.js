import { defineStore } from 'pinia'
import { ref } from 'vue'
import { members as initialMembers } from '@/lib/mockData'

export const useMembersStore = defineStore('members', () => {
  const members = ref([])

  function normalizeEmail(email) {
    return email.trim().toLowerCase()
  }

  function removeDuplicateMembers(users) {
    const seenEmails = new Set()

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
    const registeredUsers = savedRegisteredUsers
      ? JSON.parse(savedRegisteredUsers)
      : []

    const savedCurrentUser = localStorage.getItem('currentUser')
    const currentUser = savedCurrentUser
      ? [JSON.parse(savedCurrentUser)]
      : []

    const allMembers = [
      ...defaultMembers,
      ...registeredUsers,
      ...currentUser,
    ]

    members.value = removeDuplicateMembers(allMembers)
  }

  function addMember(member) {
    const newMember = {
      ...member,
      id: Date.now().toString(),
      avatar: member.name.charAt(0).toUpperCase(),
    }

    const savedRegisteredUsers = localStorage.getItem('registeredUsers')
    const registeredUsers = savedRegisteredUsers
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