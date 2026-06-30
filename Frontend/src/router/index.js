import { createRouter, createWebHistory } from 'vue-router'
import EmailVerificationView from '@/views/EmailVerificationView.vue'

function getSavedUser() {
  const savedUser = localStorage.getItem('currentUser')

  if (!savedUser) {
    return null
  }

  return JSON.parse(savedUser)
}

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: () => import('@/views/LandingView.vue'),
    },
    {
      path: '/login',
      component: () => import('@/views/LoginView.vue'),
    },
    {
      path: '/registrieren',
      component: () => import('@/views/RegisterView.vue'),
    },
    {
      path: '/wg-auswahl',
      component: () => import('@/views/ApartmentSetupView.vue'),
    },
    {
      path: '/email-bestaetigen',
      name: 'email-verification',
      component: EmailVerificationView,
    },
    {
      path: '/app',
      component: () => import('@/views/app/AppLayout.vue'),
      children: [
        { path: '', redirect: '/app/dashboard' },
        { path: 'dashboard', component: () => import('@/views/app/DashboardView.vue') },
        { path: 'ausgaben', component: () => import('@/views/app/AusgabenView.vue') },
        { path: 'aufgaben', component: () => import('@/views/app/AufgabenView.vue') },
        { path: 'mitglieder', component: () => import('@/views/app/MitgliederView.vue') },
        { path: 'einstellungen', component: () => import('@/views/app/EinstellungenView.vue') },
      ],
    },
  ],
})

router.beforeEach((to) => {
  const user = getSavedUser()

  if (to.path.startsWith('/app') && !user) {
    return '/login'
  }

  if (to.path.startsWith('/app') && user && !user.apartment_id) {
    return '/wg-auswahl'
  }

  if (to.path === '/wg-auswahl' && !user) {
    return '/login'
  }

  if (to.path === '/wg-auswahl' && user?.apartment_id) {
    return '/app/dashboard'
  }

  return true
})

export default router