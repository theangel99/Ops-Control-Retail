import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '../api'

export const useInventoryStore = defineStore('inventory', () => {
  const items = ref([])
  const loading = ref(false)
  const error = ref(null)
  const filters = ref({
    location_id: 'all',
    supplier_id: 'all',
    flags: [],
  })

  async function fetchInventory() {
    loading.value = true
    error.value = null
    try {
      const params = {}
      if (filters.value.location_id !== 'all') {
        params.location_id = filters.value.location_id
      }
      if (filters.value.supplier_id !== 'all') {
        params.supplier_id = filters.value.supplier_id
      }
      if (filters.value.flags.length > 0) {
        params.flags = filters.value.flags.join(',')
      }

      const response = await api.getInventory(params)
      items.value = response.data.data
    } catch (err) {
      error.value = err.message
      console.error('Failed to fetch inventory:', err)
    } finally {
      loading.value = false
    }
  }

  function setFilters(newFilters) {
    filters.value = { ...filters.value, ...newFilters }
    fetchInventory()
  }

  return {
    items,
    loading,
    error,
    filters,
    fetchInventory,
    setFilters,
  }
})
