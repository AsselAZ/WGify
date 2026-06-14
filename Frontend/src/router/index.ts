import { createRouter, createWebHistory } from 'vue-router'

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

export default router
