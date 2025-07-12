import MainLayout from 'layouts/MainLayout.vue'
import GuestLayout from 'layouts/GuestLayout.vue'

const routes = [
  {
    path: '/',
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      { path: '', alias: '/home', name: 'home', component: () => import('pages/IndexPage.vue') }
    ]
  },

  {
    path: '/auth',
    component: GuestLayout,
    meta: { requiresNoAuth: true },
    children: [
      { path: '', alias: '/login', name: 'login', component: () => import('pages/auth/LoginPage.vue') }
    ]
  },

  // Deixar sempre por ultimo
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
