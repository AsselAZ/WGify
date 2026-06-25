import axios from 'axios'

export const api = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

api.interceptors.request.use((config) => {
  const savedUser = localStorage.getItem('currentUser')

  if (savedUser) {
    const user = JSON.parse(savedUser)
    config.headers['X-User-Id'] = user.id
  }

  return config
})