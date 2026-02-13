import client from './client'
import {
  mockDashboard,
  mockInventory,
  mockPurchaseOrders,
  mockCashForecast,
  mockLocations,
  mockSuppliers
} from './mockData'

// Toggle between mock and real API
const USE_MOCK_DATA = import.meta.env.VITE_USE_MOCK === 'true'

// Helper to simulate API delay
const mockDelay = (data, delay = 300) => {
  return new Promise(resolve => {
    setTimeout(() => resolve({ data }), delay)
  })
}

export const api = {
  // Dashboard
  getDashboard(locationId = 'all') {
    if (USE_MOCK_DATA) {
      return mockDelay(mockDashboard)
    }
    return client.get('/dashboard', { params: { location_id: locationId } })
  },

  // Inventory
  getInventory(params = {}) {
    if (USE_MOCK_DATA) {
      let data = [...mockInventory]

      // Apply filters
      if (params.flags) {
        const flags = params.flags.split(',')
        data = data.filter(item => {
          if (flags.includes('stockout') && item.stockout_risk.has_risk) return true
          if (flags.includes('dead_stock') && item.dead_stock.is_dead_stock) return true
          if (flags.includes('low_margin') && item.margin_percent < 20) return true
          return flags.length === 0
        })
      }

      return mockDelay({ data })
    }
    return client.get('/inventory', { params })
  },

  // Purchase Orders
  getPurchaseOrders() {
    if (USE_MOCK_DATA) {
      return mockDelay({ data: mockPurchaseOrders })
    }
    return client.get('/po')
  },

  getPurchaseOrder(id) {
    if (USE_MOCK_DATA) {
      const po = mockPurchaseOrders.find(p => p.id === parseInt(id))
      return mockDelay({
        data: po,
        cash_impact: mockCashForecast
      })
    }
    return client.get(`/po/${id}`)
  },

  suggestPurchaseOrders(data) {
    if (USE_MOCK_DATA) {
      return mockDelay({
        message: 'Purchase orders created',
        data: [mockPurchaseOrders[0]]
      })
    }
    return client.post('/po/suggest', data)
  },

  transitionPurchaseOrder(id, nextStatus) {
    if (USE_MOCK_DATA) {
      const po = mockPurchaseOrders.find(p => p.id === parseInt(id))
      return mockDelay({
        message: 'Status updated',
        data: { ...po, status: nextStatus }
      })
    }
    return client.post(`/po/${id}/transition`, { next_status: nextStatus })
  },

  // Cash Forecast
  getCashForecast() {
    if (USE_MOCK_DATA) {
      return mockDelay(mockCashForecast)
    }
    return client.get('/cash/forecast')
  },

  // Locations & Suppliers
  getLocations() {
    if (USE_MOCK_DATA) {
      return mockDelay({ data: mockLocations })
    }
    return client.get('/locations')
  },

  getSuppliers() {
    if (USE_MOCK_DATA) {
      return mockDelay({ data: mockSuppliers })
    }
    return client.get('/suppliers')
  },

  // Admin
  resetDemoData() {
    if (USE_MOCK_DATA) {
      return mockDelay({ message: 'Demo data reset successfully' })
    }
    return client.post('/admin/reset')
  },
}
