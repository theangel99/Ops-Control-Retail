<script setup>
import { ref, onMounted, watch } from 'vue'
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { useDashboardStore } from '../stores/dashboard'
import { useAppStore } from '../stores/app'

ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, Title, Tooltip, Legend)

const dashboardStore = useDashboardStore()
const appStore = useAppStore()

onMounted(() => {
  dashboardStore.fetchDashboard(appStore.selectedLocation)
})

watch(() => appStore.selectedLocation, (newLoc) => {
  dashboardStore.fetchDashboard(newLoc)
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(value)
}

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    }
  }
}
</script>

<template>
  <div class="dashboard-view">
    <h2 class="page-title">Executive Dashboard</h2>

    <div v-if="dashboardStore.loading" class="loading">Loading dashboard data...</div>

    <div v-else-if="dashboardStore.data" class="dashboard-content">
      <!-- KPI Tiles -->
      <div class="kpi-grid">
        <div class="kpi-card">
          <div class="kpi-label">Revenue (30d)</div>
          <div class="kpi-value">{{ formatCurrency(dashboardStore.data.kpis.revenue_30d) }}</div>
        </div>

        <div class="kpi-card">
          <div class="kpi-label">Gross Margin (30d)</div>
          <div class="kpi-value">{{ formatCurrency(dashboardStore.data.kpis.gross_margin_30d) }}</div>
          <div class="kpi-sub">{{ dashboardStore.data.kpis.gross_margin_percent.toFixed(1) }}%</div>
        </div>

        <div class="kpi-card">
          <div class="kpi-label">Current Cash</div>
          <div class="kpi-value">{{ formatCurrency(dashboardStore.data.kpis.current_cash) }}</div>
        </div>

        <div class="kpi-card">
          <div class="kpi-label">Cash (30/60/90d)</div>
          <div class="kpi-value-small">
            {{ formatCurrency(dashboardStore.data.kpis.cash_30d) }}
          </div>
          <div class="kpi-sub">
            60d: {{ formatCurrency(dashboardStore.data.kpis.cash_60d) }} |
            90d: {{ formatCurrency(dashboardStore.data.kpis.cash_90d) }}
          </div>
        </div>

        <div class="kpi-card alert">
          <div class="kpi-label">Stockout Risk</div>
          <div class="kpi-value">{{ dashboardStore.data.kpis.stockout_risk_count }}</div>
          <div class="kpi-sub">SKUs at risk</div>
        </div>

        <div class="kpi-card warning">
          <div class="kpi-label">Dead Stock Value</div>
          <div class="kpi-value">{{ formatCurrency(dashboardStore.data.kpis.dead_stock_value) }}</div>
        </div>

        <div class="kpi-card">
          <div class="kpi-label">Inventory Turnover</div>
          <div class="kpi-value">{{ dashboardStore.data.kpis.inventory_turnover.toFixed(2) }}x</div>
          <div class="kpi-sub">Annual rate</div>
        </div>
      </div>

      <!-- Charts -->
      <div class="charts-grid">
        <div class="chart-card">
          <h3>Revenue Trend (12 Months)</h3>
          <div class="chart-container">
            <Line
              :data="{
                labels: dashboardStore.data.charts.revenue_trend.map(d => d.month),
                datasets: [{
                  label: 'Revenue',
                  data: dashboardStore.data.charts.revenue_trend.map(d => d.revenue),
                  borderColor: '#3498db',
                  backgroundColor: 'rgba(52, 152, 219, 0.1)',
                  tension: 0.3
                }]
              }"
              :options="chartOptions"
            />
          </div>
        </div>

        <div class="chart-card">
          <h3>Stockout Risk Status</h3>
          <div class="risk-stats">
            <div class="risk-item critical">
              <div class="risk-count">{{ dashboardStore.data.charts.stockout_risk_trend.critical }}</div>
              <div class="risk-label">Critical</div>
            </div>
            <div class="risk-item warning">
              <div class="risk-count">{{ dashboardStore.data.charts.stockout_risk_trend.warning }}</div>
              <div class="risk-label">Warning</div>
            </div>
          </div>
        </div>

        <div class="chart-card">
          <h3>Dead Stock Exposure</h3>
          <div class="dead-stock-stats">
            <div class="stat-item">
              <div class="stat-label">Total Value</div>
              <div class="stat-value">{{ formatCurrency(dashboardStore.data.charts.dead_stock_trend.total_value) }}</div>
            </div>
            <div class="stat-item">
              <div class="stat-label">SKU Count</div>
              <div class="stat-value">{{ dashboardStore.data.charts.dead_stock_trend.sku_count }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Lists -->
      <div class="lists-grid">
        <div class="list-card">
          <h3>Highest Stockout Risk SKUs</h3>
          <div class="list-table">
            <table>
              <thead>
                <tr>
                  <th>SKU</th>
                  <th>Product</th>
                  <th>Location</th>
                  <th>Days on Hand</th>
                  <th>Severity</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in dashboardStore.data.top_lists.top_stockout_risk" :key="item.id">
                  <td>{{ item.sku }}</td>
                  <td>{{ item.product_name }}</td>
                  <td>{{ item.location_name }}</td>
                  <td>{{ item.days_on_hand.toFixed(1) }}</td>
                  <td>
                    <span :class="['badge', item.stockout_risk.severity]">
                      {{ item.stockout_risk.severity }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="list-card">
          <h3>Largest Dead Stock Exposure</h3>
          <div class="list-table">
            <table>
              <thead>
                <tr>
                  <th>SKU</th>
                  <th>Product</th>
                  <th>On Hand</th>
                  <th>Value</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in dashboardStore.data.top_lists.top_dead_stock" :key="item.id">
                  <td>{{ item.sku }}</td>
                  <td>{{ item.product_name }}</td>
                  <td>{{ item.on_hand }}</td>
                  <td>{{ formatCurrency(item.dead_stock_value) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div class="list-card">
          <h3>Cash Low-Water Mark</h3>
          <div class="cash-projection">
            <div class="projection-item">
              <div class="projection-label">Minimum Projected Cash</div>
              <div class="projection-value">
                {{ formatCurrency(dashboardStore.data.top_lists.cash_low_water_mark.amount) }}
              </div>
              <div class="projection-date">
                Expected: {{ dashboardStore.data.top_lists.cash_low_water_mark.date }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard-view {
  max-width: 100%;
}

.page-title {
  font-size: 2.25rem;
  font-weight: 700;
  margin-bottom: 2.5rem;
  color: #1f2937;
  letter-spacing: -0.02em;
}

.loading {
  padding: 6rem;
  text-align: center;
  color: #6b7280;
  font-size: 1.125rem;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.25rem;
  margin-bottom: 2.5rem;
}

.kpi-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 1.75rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  transition: box-shadow 0.2s ease;
}

.kpi-card:hover {
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
}

.kpi-card.alert {
  border-left: 4px solid #ef4444;
}

.kpi-card.warning {
  border-left: 4px solid #f59e0b;
}

.kpi-label {
  font-size: 0.8125rem;
  color: #6b7280;
  margin-bottom: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.kpi-value {
  font-size: 2.25rem;
  font-weight: 700;
  color: #1f2937;
  line-height: 1.2;
}

.kpi-value-small {
  font-size: 1.625rem;
  font-weight: 700;
  color: #1f2937;
  line-height: 1.2;
}

.kpi-sub {
  font-size: 0.8125rem;
  color: #9ca3af;
  margin-top: 0.5rem;
}

.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(420px, 1fr));
  gap: 1.25rem;
  margin-bottom: 2.5rem;
}

.chart-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.chart-card h3 {
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.chart-container {
  height: 320px;
}

.risk-stats, .dead-stock-stats {
  display: flex;
  gap: 3rem;
  padding: 2.5rem 0;
  justify-content: center;
}

.risk-item, .stat-item {
  flex: 1;
  text-align: center;
  max-width: 200px;
}

.risk-count, .stat-value {
  font-size: 3.5rem;
  font-weight: 700;
  margin-bottom: 0.75rem;
  line-height: 1;
}

.risk-item.critical .risk-count {
  color: #ef4444;
}

.risk-item.warning .risk-count {
  color: #f59e0b;
}

.risk-label, .stat-label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.lists-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(480px, 1fr));
  gap: 1.25rem;
}

.list-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.list-card h3 {
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
  color: #1f2937;
}

.list-table {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

thead th {
  text-align: left;
  font-size: 0.6875rem;
  font-weight: 700;
  color: #6b7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 1rem 0.75rem;
  border-bottom: 2px solid #e5e7eb;
  background: #f9fafb;
}

thead th:first-child {
  border-top-left-radius: 8px;
}

thead th:last-child {
  border-top-right-radius: 8px;
}

tbody td {
  padding: 1rem 0.75rem;
  border-bottom: 1px solid #f3f4f6;
  font-size: 0.875rem;
  color: #374151;
}

tbody tr:hover {
  background: #f9fafb;
}

.badge {
  display: inline-block;
  padding: 0.375rem 0.875rem;
  border-radius: 9999px;
  font-size: 0.6875rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.025em;
}

.badge.critical {
  background: #fef2f2;
  color: #dc2626;
}

.badge.warning {
  background: #fffbeb;
  color: #d97706;
}

.cash-projection {
  padding: 2.5rem 0;
}

.projection-item {
  text-align: center;
}

.projection-label {
  font-size: 0.875rem;
  color: #6b7280;
  margin-bottom: 1.25rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.projection-value {
  font-size: 3rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 0.75rem;
  line-height: 1;
}

.projection-date {
  font-size: 0.875rem;
  color: #9ca3af;
}
</style>
