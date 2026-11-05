import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import api, { ensureCsrfCookie } from '../api/client'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const loading = ref(false)
  const error = ref(null)

  const isAuthenticated = computed(() => !!user.value)

  async function fetchUser() {
    try {
      const { data } = await api.get('/api/user')
      user.value = data.user
      return user.value
    } catch {
      user.value = null
      return null
    }
  }

  async function login(email, password) {
    loading.value = true
    error.value = null

    try {
      await ensureCsrfCookie()
      const { data } = await api.post('/api/login', { email, password })
      user.value = data.user
      return user.value
    } catch (e) {
      error.value = e.response?.data?.message || 'Login failed'
      if (e.response?.data?.errors) {
        error.value = Object.values(e.response.data.errors).flat().join(' ')
      }
      throw e
    } finally {
      loading.value = false
    }
  }

  async function logout() {
    try {
      await api.post('/api/logout')
    } finally {
      user.value = null
    }
  }

  return {
    user,
    loading,
    error,
    isAuthenticated,
    fetchUser,
    login,
    logout,
  }
})
