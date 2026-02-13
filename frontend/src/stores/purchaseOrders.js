import { defineStore } from 'pinia'
import { ref } from 'vue'
import { api } from '../api'

export const usePurchaseOrderStore = defineStore('purchaseOrders', () => {
  const orders = ref([])
  const selectedOrder = ref(null)
  const loading = ref(false)
  const error = ref(null)

  async function fetchOrders() {
    loading.value = true
    error.value = null
    try {
      const response = await api.getPurchaseOrders()
      orders.value = response.data.data
    } catch (err) {
      error.value = err.message
      console.error('Failed to fetch purchase orders:', err)
    } finally {
      loading.value = false
    }
  }

  async function fetchOrder(id) {
    loading.value = true
    error.value = null
    try {
      const response = await api.getPurchaseOrder(id)
      selectedOrder.value = response.data.data
      return response.data
    } catch (err) {
      error.value = err.message
      console.error('Failed to fetch purchase order:', err)
    } finally {
      loading.value = false
    }
  }

  async function suggestOrders(data) {
    loading.value = true
    error.value = null
    try {
      const response = await api.suggestPurchaseOrders(data)
      await fetchOrders() // Refresh list
      return response.data
    } catch (err) {
      error.value = err.message
      console.error('Failed to suggest purchase orders:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  async function transitionOrder(id, nextStatus) {
    loading.value = true
    error.value = null
    try {
      const response = await api.transitionPurchaseOrder(id, nextStatus)
      await fetchOrders() // Refresh list
      return response.data
    } catch (err) {
      error.value = err.message
      console.error('Failed to transition purchase order:', err)
      throw err
    } finally {
      loading.value = false
    }
  }

  return {
    orders,
    selectedOrder,
    loading,
    error,
    fetchOrders,
    fetchOrder,
    suggestOrders,
    transitionOrder,
  }
})
