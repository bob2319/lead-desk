import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import HomePage from '../pages/HomePage.vue'
import LoginPage from '../pages/LoginPage.vue'
import LeadsPage from '../pages/admin/LeadsPage.vue'
import LeadShowPage from '../pages/admin/LeadShowPage.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomePage,
    },
    {
      path: '/login',
      name: 'login',
      component: LoginPage,
      meta: { guest: true },
    },
    {
      path: '/admin/leads',
      name: 'admin.leads',
      component: LeadsPage,
      meta: { requiresAuth: true },
    },
    {
      path: '/admin/leads/:id',
      name: 'admin.leads.show',
      component: LeadShowPage,
      meta: { requiresAuth: true },
    },
  ],
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  if (auth.user === null && (to.meta.requiresAuth || to.meta.guest)) {
    await auth.fetchUser()
  }

  if (to.meta.requiresAuth && !auth.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  }

  if (to.meta.guest && auth.isAuthenticated) {
    return { name: 'admin.leads' }
  }

  return true
})

export default router
