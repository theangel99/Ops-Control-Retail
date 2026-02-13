import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '../api'

export const useDashboardStore = defineStore('dashboard', () => {
  const data = ref(null)
  const loading = ref(false)
  const error = ref(null)

  async function fetchDashboard(locationId = 'all') {
    loading.value = true
    error.value = null
    try {
      const response = await api.getDashboard(locationId)
      data.value = response.data
    } catch (err) {
      error.value = err.message
      console.error('Failed to fetch dashboard:', err)
    } finally {
      loading.value = false
    }
  }

  return {
    data,
    loading,
    error,
    fetchDashboard,
  }
})
