<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import { useInventoryStore } from '../stores/inventory'
import { useAppStore } from '../stores/app'
import { usePurchaseOrderStore } from '../stores/purchaseOrders'
import { api } from '../api'

const inventoryStore = useInventoryStore()
const appStore = useAppStore()
const poStore = usePurchaseOrderStore()

const suppliers = ref([])
const locations = ref([])
const selectedItems = ref([])

onMounted(async () => {
  try {
    const [suppliersRes, locationsRes] = await Promise.all([
      api.getSuppliers(),
      api.getLocations()
    ])
    suppliers.value = [{ id: 'all', name: 'All Suppliers' }, ...suppliersRes.data.data]
    locations.value = [{ id: 'all', name: 'All Locations' }, ...locationsRes.data.data]
  } catch (error) {
    console.error('Failed to load filters:', error)
  }

  inventoryStore.fetchInventory()
})

watch(() => appStore.selectedLocation, (newLoc) => {
  inventoryStore.setFilters({ location_id: newLoc })
})

const handleSupplierChange = (event) => {
  inventoryStore.setFilters({ supplier_id: event.target.value })
}

const handleFlagToggle = (flag) => {
  const currentFlags = [...inventoryStore.filters.flags]
  const index = currentFlags.indexOf(flag)
  if (index > -1) {
    currentFlags.splice(index, 1)
  } else {
    currentFlags.push(flag)
  }
  inventoryStore.setFilters({ flags: currentFlags })
}

const toggleItemSelection = (itemId) => {
  const index = selectedItems.value.indexOf(itemId)
  if (index > -1) {
    selectedItems.value.splice(index, 1)
  } else {
    selectedItems.value.push(itemId)
  }
}

const handleGeneratePO = async () => {
  if (selectedItems.value.length === 0) {
    alert('Please select at least one item')
    return
  }

  try {
    const locationId = appStore.selectedLocation === 'all' ? 1 : appStore.selectedLocation
    await poStore.suggestOrders({
      location_id: locationId,
      product_ids: selectedItems.value
    })
    alert('Purchase orders generated successfully!')
    selectedItems.value = []
  } catch (error) {
    alert('Failed to generate PO: ' + error.message)
  }
}

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value)
}
</script>

<template>
  <div class="inventory-view">
    <h2 class="page-title">Inventory Control Panel</h2>

    <!-- Filters -->
    <div class="filters-bar">
      <div class="filter-group">
        <label>Supplier:</label>
        <select :value="inventoryStore.filters.supplier_id" @change="handleSupplierChange">
          <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">
            {{ sup.name }}
          </option>
        </select>
      </div>

      <div class="filter-group flags">
        <label>Filters:</label>
        <button
          :class="['flag-button', { active: inventoryStore.filters.flags.includes('stockout') }]"
          @click="handleFlagToggle('stockout')"
        >
          Stockout Risk
        </button>
        <button
          :class="['flag-button', { active: inventoryStore.filters.flags.includes('dead_stock') }]"
          @click="handleFlagToggle('dead_stock')"
        >
          Dead Stock
        </button>
        <button
          :class="['flag-button', { active: inventoryStore.filters.flags.includes('low_margin') }]"
          @click="handleFlagToggle('low_margin')"
        >
          Low Margin
        </button>
      </div>

      <div class="filter-actions">
        <button
          @click="handleGeneratePO"
          class="generate-po-button"
          :disabled="selectedItems.length === 0"
        >
          Generate PO for Selected ({{ selectedItems.length }})
        </button>
      </div>
    </div>

    <!-- Inventory Table -->
    <div v-if="inventoryStore.loading" class="loading">Loading inventory data...</div>

    <div v-else-if="inventoryStore.items.length > 0" class="inventory-table-container">
      <table class="inventory-table">
        <thead>
          <tr>
            <th><input type="checkbox" disabled /></th>
            <th>SKU</th>
            <th>Product</th>
            <th>Location</th>
            <th>Supplier</th>
            <th>On Hand</th>
            <th>On Order</th>
            <th>Velocity</th>
            <th>DOH</th>
            <th>Lead Time</th>
            <th>Safety Stock</th>
            <th>Reorder Point</th>
            <th>Suggested Qty</th>
            <th>Margin %</th>
            <th>Flags</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in inventoryStore.items" :key="item.id" :class="{ selected: selectedItems.includes(item.product_id) }">
            <td>
              <input
                type="checkbox"
                :checked="selectedItems.includes(item.product_id)"
                @change="toggleItemSelection(item.product_id)"
              />
            </td>
            <td>{{ item.sku }}</td>
            <td>{{ item.product_name }}</td>
            <td>{{ item.location_name }}</td>
            <td>{{ item.supplier_name }}</td>
            <td>{{ item.on_hand }}</td>
            <td>{{ item.on_order }}</td>
            <td>{{ item.velocity }}</td>
            <td>{{ item.days_on_hand }}</td>
            <td>{{ item.lead_time_days }}</td>
            <td>{{ item.safety_stock_days }}</td>
            <td>{{ item.reorder_point }}</td>
            <td class="suggested">{{ item.suggested_reorder_qty }}</td>
            <td>{{ item.margin_percent.toFixed(1) }}%</td>
            <td>
              <div class="flags-cell">
                <span
                  v-if="item.stockout_risk.has_risk"
                  :class="['flag-badge', item.stockout_risk.severity]"
                >
                  {{ item.stockout_risk.severity }}
                </span>
                <span
                  v-if="item.dead_stock.is_dead_stock"
                  class="flag-badge dead-stock"
                >
                  Dead Stock
                </span>
                <span
                  v-if="item.margin_percent < 20"
                  class="flag-badge low-margin"
                >
                  Low Margin
                </span>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-else class="empty-state">
      No inventory items match the current filters.
    </div>
  </div>
</template>

<style scoped>
.inventory-view {
  max-width: 100%;
}

.page-title {
  font-size: 2.25rem;
  font-weight: 700;
  margin-bottom: 2.5rem;
  color: #1f2937;
  letter-spacing: -0.02em;
}

.filters-bar {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 2rem;
  margin-bottom: 1.5rem;
  display: flex;
  gap: 2rem;
  align-items: center;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.filter-group label {
  font-size: 0.875rem;
  font-weight: 500;
  color: #666;
}

.filter-group select {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 0.875rem;
}

.filter-group.flags {
  gap: 0.75rem;
}

.flag-button {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: #fff;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;
}

.flag-button:hover {
  border-color: #3498db;
  color: #3498db;
}

.flag-button.active {
  background: #3498db;
  color: #fff;
  border-color: #3498db;
}

.filter-actions {
  margin-left: auto;
}

.generate-po-button {
  padding: 0.75rem 1.5rem;
  background: #27ae60;
  color: #fff;
  border: none;
  border-radius: 4px;
  font-size: 0.875rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.2s;
}

.generate-po-button:hover:not(:disabled) {
  background: #229954;
}

.generate-po-button:disabled {
  background: #95a5a6;
  cursor: not-allowed;
}

.loading, .empty-state {
  padding: 4rem;
  text-align: center;
  color: #666;
}

.inventory-table-container {
  background: #fff;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  overflow-x: auto;
}

.inventory-table {
  width: 100%;
  border-collapse: collapse;
}

.inventory-table thead th {
  background: #f8f9fa;
  text-align: left;
  font-size: 0.75rem;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  padding: 1rem 0.75rem;
  border-bottom: 2px solid #e0e0e0;
  white-space: nowrap;
}

.inventory-table tbody tr {
  border-bottom: 1px solid #f0f0f0;
  transition: background 0.2s;
}

.inventory-table tbody tr:hover {
  background: #f8f9fa;
}

.inventory-table tbody tr.selected {
  background: #e8f4f8;
}

.inventory-table tbody td {
  padding: 0.75rem;
  font-size: 0.875rem;
  white-space: nowrap;
}

.inventory-table tbody td.suggested {
  font-weight: 600;
  color: #27ae60;
}

.flags-cell {
  display: flex;
  gap: 0.25rem;
  flex-wrap: wrap;
}

.flag-badge {
  display: inline-block;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.7rem;
  font-weight: 600;
  text-transform: uppercase;
}

.flag-badge.critical {
  background: #fee;
  color: #e74c3c;
}

.flag-badge.warning {
  background: #fef5e7;
  color: #f39c12;
}

.flag-badge.dead-stock {
  background: #fdecea;
  color: #c0392b;
}

.flag-badge.low-margin {
  background: #e8f4f8;
  color: #2980b9;
}
</style>
