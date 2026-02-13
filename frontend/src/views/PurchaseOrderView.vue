<script setup>
import { ref, onMounted, computed } from 'vue'
import { usePurchaseOrderStore } from '../stores/purchaseOrders'

const poStore = usePurchaseOrderStore()
const selectedPO = ref(null)
const cashImpact = ref(null)

onMounted(() => {
  poStore.fetchOrders()
})

const groupedOrders = computed(() => {
  const groups = {
    draft: [],
    submitted: [],
    approved: [],
    ordered: [],
    received: []
  }

  poStore.orders.forEach(order => {
    if (groups[order.status]) {
      groups[order.status].push(order)
    }
  })

  return groups
})

const handleViewDetails = async (po) => {
  selectedPO.value = po
  const result = await poStore.fetchOrder(po.id)
  if (result) {
    cashImpact.value = result.cash_impact
  }
}

const handleTransition = async (po) => {
  const transitions = {
    draft: 'submitted',
    submitted: 'approved',
    approved: 'ordered',
    ordered: 'received'
  }

  const nextStatus = transitions[po.status]
  if (!nextStatus) return

  if (confirm(`Transition PO ${po.po_number} to ${nextStatus}?`)) {
    try {
      await poStore.transitionOrder(po.id, nextStatus)
      alert('PO status updated successfully!')
      if (selectedPO.value && selectedPO.value.id === po.id) {
        await handleViewDetails(po)
      }
    } catch (error) {
      alert('Failed to update PO: ' + error.message)
    }
  }
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value)
}

const formatDate = (dateString) => {
  if (!dateString) return 'N/A'
  return new Date(dateString).toLocaleDateString()
}

const statusColor = (status) => {
  const colors = {
    draft: '#95a5a6',
    submitted: '#3498db',
    approved: '#f39c12',
    ordered: '#9b59b6',
    received: '#27ae60'
  }
  return colors[status] || '#95a5a6'
}
</script>

<template>
  <div class="po-view">
    <h2 class="page-title">Purchase Workflow</h2>

    <div class="po-layout">
      <!-- PO List -->
      <div class="po-list-section">
        <div v-if="poStore.loading && poStore.orders.length === 0" class="loading">
          Loading purchase orders...
        </div>

        <div v-else class="po-groups">
          <div v-for="(orders, status) in groupedOrders" :key="status" class="po-group">
            <div class="group-header" :style="{ borderLeftColor: statusColor(status) }">
              <h3>{{ status.charAt(0).toUpperCase() + status.slice(1) }}</h3>
              <span class="count">{{ orders.length }}</span>
            </div>

            <div v-if="orders.length > 0" class="po-cards">
              <div
                v-for="po in orders"
                :key="po.id"
                :class="['po-card', { active: selectedPO && selectedPO.id === po.id }]"
                @click="handleViewDetails(po)"
              >
                <div class="po-card-header">
                  <div class="po-number">{{ po.po_number }}</div>
                  <div class="po-total">{{ formatCurrency(po.total_cost) }}</div>
                </div>
                <div class="po-card-body">
                  <div class="po-info">
                    <span class="label">Supplier:</span>
                    <span>{{ po.supplier.name }}</span>
                  </div>
                  <div class="po-info">
                    <span class="label">Location:</span>
                    <span>{{ po.location.name }}</span>
                  </div>
                  <div class="po-info">
                    <span class="label">Expected:</span>
                    <span>{{ formatDate(po.expected_delivery_date) }}</span>
                  </div>
                </div>
              </div>
            </div>

            <div v-else class="empty-group">
              No {{ status }} orders
            </div>
          </div>
        </div>
      </div>

      <!-- PO Details -->
      <div class="po-details-section">
        <div v-if="!selectedPO" class="no-selection">
          Select a purchase order to view details
        </div>

        <div v-else class="po-details">
          <div class="details-header">
            <h3>{{ selectedPO.po_number }}</h3>
            <span class="status-badge" :style="{ backgroundColor: statusColor(selectedPO.status) }">
              {{ selectedPO.status }}
            </span>
          </div>

          <div class="details-section">
            <h4>Order Information</h4>
            <div class="info-grid">
              <div class="info-item">
                <span class="info-label">Supplier</span>
                <span class="info-value">{{ selectedPO.supplier.name }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Destination</span>
                <span class="info-value">{{ selectedPO.location.name }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Total Cost</span>
                <span class="info-value">{{ formatCurrency(selectedPO.total_cost) }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Expected Delivery</span>
                <span class="info-value">{{ formatDate(selectedPO.expected_delivery_date) }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Ordered At</span>
                <span class="info-value">{{ formatDate(selectedPO.ordered_at) }}</span>
              </div>
              <div class="info-item">
                <span class="info-label">Received At</span>
                <span class="info-value">{{ formatDate(selectedPO.received_at) }}</span>
              </div>
            </div>
          </div>

          <div class="details-section">
            <h4>Line Items</h4>
            <table class="line-items-table">
              <thead>
                <tr>
                  <th>SKU</th>
                  <th>Product</th>
                  <th>Quantity</th>
                  <th>Unit Cost</th>
                  <th>Total</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="line in selectedPO.lines" :key="line.id">
                  <td>{{ line.product.sku }}</td>
                  <td>{{ line.product.name }}</td>
                  <td>{{ line.qty }}</td>
                  <td>{{ formatCurrency(line.unit_cost) }}</td>
                  <td>{{ formatCurrency(line.qty * line.unit_cost) }}</td>
                </tr>
              </tbody>
            </table>
          </div>

          <div v-if="cashImpact" class="details-section">
            <h4>Cash Impact Preview</h4>
            <div class="cash-grid">
              <div class="cash-item">
                <span class="cash-label">Current Cash</span>
                <span class="cash-value">{{ formatCurrency(cashImpact.current_cash) }}</span>
              </div>
              <div class="cash-item">
                <span class="cash-label">30 Days</span>
                <span class="cash-value">{{ formatCurrency(cashImpact.projections[30].projected_cash) }}</span>
              </div>
              <div class="cash-item">
                <span class="cash-label">60 Days</span>
                <span class="cash-value">{{ formatCurrency(cashImpact.projections[60].projected_cash) }}</span>
              </div>
              <div class="cash-item">
                <span class="cash-label">90 Days</span>
                <span class="cash-value">{{ formatCurrency(cashImpact.projections[90].projected_cash) }}</span>
              </div>
            </div>
          </div>

          <div class="details-actions">
            <button
              v-if="selectedPO.status !== 'received'"
              @click="handleTransition(selectedPO)"
              class="transition-button"
            >
              {{
                selectedPO.status === 'draft' ? 'Submit' :
                selectedPO.status === 'submitted' ? 'Approve' :
                selectedPO.status === 'approved' ? 'Mark as Ordered' :
                'Mark as Received'
              }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.po-view {
  max-width: 1800px;
  margin: 0 auto;
}

.page-title {
  font-size: 2rem;
  font-weight: 600;
  margin-bottom: 2rem;
  color: #2c3e50;
}

.po-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}

.loading {
  padding: 4rem;
  text-align: center;
  color: #666;
}

.po-groups {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.po-group {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow: hidden;
}

.group-header {
  background: #f8f9fa;
  padding: 1rem 1.5rem;
  border-left: 4px solid #95a5a6;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.group-header h3 {
  font-size: 1rem;
  font-weight: 600;
  color: #2c3e50;
}

.count {
  background: #e0e0e0;
  padding: 0.25rem 0.75rem;
  border-radius: 12px;
  font-size: 0.875rem;
  font-weight: 600;
  color: #666;
}

.po-cards {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.po-card {
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  padding: 1rem;
  cursor: pointer;
  transition: all 0.2s;
}

.po-card:hover {
  border-color: #3498db;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.po-card.active {
  border-color: #3498db;
  background: #e8f4f8;
}

.po-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.75rem;
}

.po-number {
  font-weight: 600;
  color: #2c3e50;
}

.po-total {
  font-weight: 600;
  color: #27ae60;
}

.po-card-body {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.po-info {
  display: flex;
  justify-content: space-between;
  font-size: 0.875rem;
}

.po-info .label {
  color: #666;
}

.empty-group {
  padding: 2rem;
  text-align: center;
  color: #999;
  font-size: 0.875rem;
}

.po-details-section {
  position: sticky;
  top: 1rem;
  height: fit-content;
}

.no-selection {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 4rem;
  text-align: center;
  color: #999;
}

.po-details {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 1.5rem;
}

.details-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid #e0e0e0;
}

.details-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
}

.status-badge {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  color: #fff;
  font-size: 0.875rem;
  font-weight: 600;
  text-transform: uppercase;
}

.details-section {
  margin-bottom: 1.5rem;
}

.details-section h4 {
  font-size: 1rem;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 1rem;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.info-label {
  font-size: 0.75rem;
  color: #666;
  text-transform: uppercase;
  font-weight: 600;
}

.info-value {
  font-size: 0.875rem;
  color: #2c3e50;
}

.line-items-table {
  width: 100%;
  border-collapse: collapse;
}

.line-items-table thead th {
  text-align: left;
  font-size: 0.75rem;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  padding: 0.75rem 0.5rem;
  border-bottom: 2px solid #e0e0e0;
}

.line-items-table tbody td {
  padding: 0.75rem 0.5rem;
  border-bottom: 1px solid #f0f0f0;
  font-size: 0.875rem;
}

.cash-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}

.cash-item {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 6px;
}

.cash-label {
  font-size: 0.75rem;
  color: #666;
  font-weight: 600;
  text-transform: uppercase;
}

.cash-value {
  font-size: 1.125rem;
  font-weight: 600;
  color: #2c3e50;
}

.details-actions {
  margin-top: 1.5rem;
  padding-top: 1.5rem;
  border-top: 2px solid #e0e0e0;
}

.transition-button {
  width: 100%;
  padding: 1rem;
  background: #3498db;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.transition-button:hover {
  background: #2980b9;
}
</style>
