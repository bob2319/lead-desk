import { defineStore } from 'pinia'
import { ref } from 'vue'
import api, { ensureCsrfCookie } from '../api/client'

export const useLeadsStore = defineStore('leads', () => {
  const items = ref([])
  const meta = ref(null)
  const current = ref(null)
  const loading = ref(false)
  const error = ref(null)
  const formErrors = ref({})

  async function submitLead(payload) {
    loading.value = true
    error.value = null
    formErrors.value = {}

    try {
      await ensureCsrfCookie()
      const { data } = await api.post('/api/leads', payload)
      return data.data
    } catch (e) {
      formErrors.value = e.response?.data?.errors || {}
      error.value = e.response?.data?.message || 'Could not submit lead'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function fetchLeads({ status = '', search = '', page = 1 } = {}) {
    loading.value = true
    error.value = null

    try {
      const { data } = await api.get('/api/leads', {
        params: { status: status || undefined, search: search || undefined, page },
      })
      items.value = data.data
      meta.value = data.meta
      return data
    } catch (e) {
      error.value = e.response?.data?.message || 'Could not load leads'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function fetchLead(id) {
    loading.value = true
    error.value = null

    try {
      const { data } = await api.get(`/api/leads/${id}`)
      current.value = data.data
      return current.value
    } catch (e) {
      error.value = e.response?.data?.message || 'Could not load lead'
      throw e
    } finally {
      loading.value = false
    }
  }

  async function updateLead(id, payload) {
    loading.value = true
    error.value = null
    formErrors.value = {}

    try {
      const { data } = await api.patch(`/api/leads/${id}`, payload)
      current.value = data.data
      const index = items.value.findIndex((lead) => lead.id === id)
      if (index !== -1) {
        items.value[index] = data.data
      }
      return current.value
    } catch (e) {
      formErrors.value = e.response?.data?.errors || {}
      error.value = e.response?.data?.message || 'Could not update lead'
      throw e
    } finally {
      loading.value = false
    }
  }

  return {
    items,
    meta,
    current,
    loading,
    error,
    formErrors,
    submitLead,
    fetchLeads,
    fetchLead,
    updateLead,
  }
})
