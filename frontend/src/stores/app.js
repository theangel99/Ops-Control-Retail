import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useAppStore = defineStore('app', () => {
  const role = ref('Executive')
  const selectedLocation = ref('all')
  const isLoading = ref(false)

  function setRole(newRole) {
    role.value = newRole
  }

  function setLocation(locationId) {
    selectedLocation.value = locationId
  }

  function setLoading(loading) {
    isLoading.value = loading
  }

  return {
    role,
    selectedLocation,
    isLoading,
    setRole,
    setLocation,
    setLoading,
  }
})
