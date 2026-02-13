import { createRouter, createWebHistory } from 'vue-router'
import DashboardView from '../views/DashboardView.vue'
import InventoryView from '../views/InventoryView.vue'
import PurchaseOrderView from '../views/PurchaseOrderView.vue'

const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: DashboardView,
  },
  {
    path: '/inventory',
    name: 'Inventory',
    component: InventoryView,
  },
  {
    path: '/purchase-orders',
    name: 'PurchaseOrders',
    component: PurchaseOrderView,
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
