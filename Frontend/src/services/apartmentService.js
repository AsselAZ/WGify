import { api } from '@/api'

export const inviteUser = (email) => {
  return api.post('/apartments/invite', {
    email,
  })
}