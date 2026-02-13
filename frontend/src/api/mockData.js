// Mock data for frontend-only demo

export const mockDashboard = {
  kpis: {
    revenue_30d: 487650.00,
    gross_margin_30d: 146295.00,
    gross_margin_percent: 30.0,
    current_cash: 450000.00,
    cash_30d: 425000.00,
    cash_60d: 398000.00,
    cash_90d: 375000.00,
    stockout_risk_count: 23,
    dead_stock_value: 125400.00,
    inventory_turnover: 4.5
  },
  charts: {
    revenue_trend: [
      { month: 'Jan 2024', revenue: 380000 },
      { month: 'Feb 2024', revenue: 350000 },
      { month: 'Mar 2024', revenue: 420000 },
      { month: 'Apr 2024', revenue: 445000 },
      { month: 'May 2024', revenue: 480000 },
      { month: 'Jun 2024', revenue: 510000 },
      { month: 'Jul 2024', revenue: 540000 },
      { month: 'Aug 2024', revenue: 520000 },
      { month: 'Sep 2024', revenue: 460000 },
      { month: 'Oct 2024', revenue: 490000 },
      { month: 'Nov 2024', revenue: 580000 },
      { month: 'Dec 2024', revenue: 620000 }
    ],
    stockout_risk_trend: {
      critical: 8,
      warning: 15
    },
    dead_stock_trend: {
      total_value: 125400.00,
      sku_count: 18
    }
  },
  top_lists: {
    top_stockout_risk: [
      {
        id: 1,
        sku: 'HV-001',
        product_name: 'Premium Wireless Headphones',
        location_name: 'Store - New York',
        days_on_hand: 8.5,
        stockout_risk: { has_risk: true, severity: 'critical' }
      },
      {
        id: 2,
        sku: 'HV-002',
        product_name: 'Smart Fitness Tracker',
        location_name: 'Store - Los Angeles',
        days_on_hand: 12.3,
        stockout_risk: { has_risk: true, severity: 'critical' }
      },
      {
        id: 3,
        sku: 'HV-003',
        product_name: 'Yoga Mat Premium',
        location_name: 'Store - Chicago',
        days_on_hand: 18.7,
        stockout_risk: { has_risk: true, severity: 'warning' }
      },
      {
        id: 4,
        sku: 'HV-004',
        product_name: 'LED Desk Lamp',
        location_name: 'Store - Houston',
        days_on_hand: 15.2,
        stockout_risk: { has_risk: true, severity: 'warning' }
      },
      {
        id: 5,
        sku: 'HV-005',
        product_name: 'Stainless Steel Water Bottle',
        location_name: 'Store - Phoenix',
        days_on_hand: 10.8,
        stockout_risk: { has_risk: true, severity: 'critical' }
      }
    ],
    top_dead_stock: [
      {
        id: 10,
        sku: 'DS-001',
        product_name: 'VHS Player Retro',
        on_hand: 285,
        unit_cost: 50.00,
        dead_stock_value: 14250.00,
        dead_stock: { is_dead_stock: true }
      },
      {
        id: 11,
        sku: 'DS-006',
        product_name: 'Film Camera 35mm',
        on_hand: 180,
        unit_cost: 85.00,
        dead_stock_value: 15300.00,
        dead_stock: { is_dead_stock: true }
      },
      {
        id: 12,
        sku: 'DS-014',
        product_name: 'MiniDisc Player',
        on_hand: 165,
        unit_cost: 75.00,
        dead_stock_value: 12375.00,
        dead_stock: { is_dead_stock: true }
      },
      {
        id: 13,
        sku: 'DS-003',
        product_name: 'Slide Projector Classic',
        on_hand: 220,
        unit_cost: 65.00,
        dead_stock_value: 14300.00,
        dead_stock: { is_dead_stock: true }
      }
    ],
    cash_low_water_mark: {
      amount: 368500.00,
      date: '2025-04-15'
    }
  }
}

export const mockInventory = [
  {
    id: 1,
    product_id: 1,
    location_id: 2,
    sku: 'HV-001',
    product_name: 'Premium Wireless Headphones',
    category: 'Electronics',
    location_name: 'Store - New York',
    supplier_name: 'Global Supply Co',
    supplier_id: 1,
    on_hand: 15,
    on_order: 0,
    unit_cost: 45.00,
    unit_price: 89.99,
    margin: 44.99,
    margin_percent: 50.0,
    velocity: 2.5,
    days_on_hand: 6.0,
    lead_time_days: 60,
    safety_stock_days: 21,
    reorder_point: 203,
    suggested_reorder_qty: 210,
    stockout_risk: { has_risk: true, severity: 'critical', threshold: 81 },
    dead_stock: { is_dead_stock: false },
    inventory_age_days: 15
  },
  {
    id: 2,
    product_id: 2,
    location_id: 3,
    sku: 'HV-002',
    product_name: 'Smart Fitness Tracker',
    category: 'Electronics',
    location_name: 'Store - Los Angeles',
    supplier_name: 'Pacific Imports',
    supplier_id: 2,
    on_hand: 22,
    on_order: 10,
    unit_cost: 30.00,
    unit_price: 69.99,
    margin: 39.99,
    margin_percent: 57.1,
    velocity: 1.8,
    days_on_hand: 12.2,
    lead_time_days: 60,
    safety_stock_days: 21,
    reorder_point: 146,
    suggested_reorder_qty: 130,
    stockout_risk: { has_risk: true, severity: 'critical', threshold: 81 },
    dead_stock: { is_dead_stock: false },
    inventory_age_days: 8
  },
  {
    id: 3,
    product_id: 10,
    location_id: 2,
    sku: 'DS-001',
    product_name: 'VHS Player Retro',
    category: 'Electronics',
    location_name: 'Store - New York',
    supplier_name: 'Global Supply Co',
    supplier_id: 1,
    on_hand: 285,
    on_order: 0,
    unit_cost: 50.00,
    unit_price: 79.99,
    margin: 29.99,
    margin_percent: 37.5,
    velocity: 0.01,
    days_on_hand: 999,
    lead_time_days: 60,
    safety_stock_days: 14,
    reorder_point: 1,
    suggested_reorder_qty: 0,
    stockout_risk: { has_risk: false },
    dead_stock: { is_dead_stock: true, reason: 'Low velocity + aged inventory', age_days: 185, velocity: 0.01 },
    inventory_age_days: 185
  },
  {
    id: 4,
    product_id: 35,
    location_id: 2,
    sku: 'LM-001',
    product_name: '4K TV 65 inch',
    category: 'Electronics',
    location_name: 'Store - New York',
    supplier_name: 'Global Supply Co',
    supplier_id: 1,
    on_hand: 45,
    on_order: 20,
    unit_cost: 580.00,
    unit_price: 599.99,
    margin: 19.99,
    margin_percent: 3.3,
    velocity: 0.8,
    days_on_hand: 56.3,
    lead_time_days: 60,
    safety_stock_days: 14,
    reorder_point: 59,
    suggested_reorder_qty: 7,
    stockout_risk: { has_risk: false },
    dead_stock: { is_dead_stock: false },
    inventory_age_days: 42
  }
]

export const mockPurchaseOrders = [
  {
    id: 1,
    po_number: 'PO-65A7F8B2C',
    supplier_id: 1,
    location_id: 2,
    status: 'draft',
    total_cost: 12450.00,
    expected_delivery_date: '2025-04-15',
    ordered_at: null,
    received_at: null,
    supplier: { id: 1, name: 'Global Supply Co' },
    location: { id: 2, name: 'Store - New York' },
    lines: [
      {
        id: 1,
        product_id: 1,
        qty: 150,
        unit_cost: 45.00,
        product: { id: 1, sku: 'HV-001', name: 'Premium Wireless Headphones' }
      },
      {
        id: 2,
        product_id: 3,
        qty: 200,
        unit_cost: 12.00,
        product: { id: 3, sku: 'HV-003', name: 'Yoga Mat Premium' }
      }
    ]
  },
  {
    id: 2,
    po_number: 'PO-45B3C9A1D',
    supplier_id: 2,
    location_id: 3,
    status: 'submitted',
    total_cost: 8900.00,
    expected_delivery_date: '2025-04-18',
    ordered_at: null,
    received_at: null,
    supplier: { id: 2, name: 'Pacific Imports' },
    location: { id: 3, name: 'Store - Los Angeles' },
    lines: [
      {
        id: 3,
        product_id: 2,
        qty: 180,
        unit_cost: 30.00,
        product: { id: 2, sku: 'HV-002', name: 'Smart Fitness Tracker' }
      }
    ]
  },
  {
    id: 3,
    po_number: 'PO-78D2E5F3A',
    supplier_id: 1,
    location_id: 4,
    status: 'approved',
    total_cost: 15600.00,
    expected_delivery_date: '2025-04-20',
    ordered_at: '2025-02-19',
    received_at: null,
    supplier: { id: 1, name: 'Global Supply Co' },
    location: { id: 4, name: 'Store - Chicago' },
    lines: [
      {
        id: 4,
        product_id: 4,
        qty: 120,
        unit_cost: 18.00,
        product: { id: 4, sku: 'HV-004', name: 'LED Desk Lamp' }
      },
      {
        id: 5,
        product_id: 7,
        qty: 300,
        unit_cost: 35.00,
        product: { id: 7, sku: 'HV-007', name: 'Running Shoes Pro' }
      }
    ]
  },
  {
    id: 4,
    po_number: 'PO-92A1B4C8E',
    supplier_id: 3,
    location_id: 5,
    status: 'ordered',
    total_cost: 6800.00,
    expected_delivery_date: '2025-04-12',
    ordered_at: '2025-02-11',
    received_at: null,
    supplier: { id: 3, name: 'Domestic Goods Inc' },
    location: { id: 5, name: 'Store - Houston' },
    lines: [
      {
        id: 6,
        product_id: 8,
        qty: 220,
        unit_cost: 22.00,
        product: { id: 8, sku: 'HV-008', name: 'Backpack Tactical' }
      }
    ]
  },
  {
    id: 5,
    po_number: 'PO-34F7D2B5C',
    supplier_id: 2,
    location_id: 2,
    status: 'received',
    total_cost: 9200.00,
    expected_delivery_date: '2025-02-01',
    ordered_at: '2024-12-03',
    received_at: '2025-02-01',
    supplier: { id: 2, name: 'Pacific Imports' },
    location: { id: 2, name: 'Store - New York' },
    lines: [
      {
        id: 7,
        product_id: 6,
        qty: 250,
        unit_cost: 25.00,
        product: { id: 6, sku: 'HV-006', name: 'Bluetooth Speaker Portable' }
      }
    ]
  }
]

export const mockCashForecast = {
  current_cash: 450000.00,
  projections: {
    30: {
      date: '2025-03-15',
      projected_cash: 425000.00,
      total_inflows: 485000.00,
      total_outflows: 510000.00
    },
    60: {
      date: '2025-04-14',
      projected_cash: 398000.00,
      total_inflows: 460000.00,
      total_outflows: 487000.00
    },
    90: {
      date: '2025-05-14',
      projected_cash: 375000.00,
      total_inflows: 470000.00,
      total_outflows: 493000.00
    }
  },
  low_water_mark: {
    amount: 368500.00,
    date: '2025-04-28'
  }
}

export const mockLocations = [
  { id: 'all', name: 'All Locations' },
  { id: 1, name: 'Central Warehouse', code: 'WH-01', is_warehouse: true },
  { id: 2, name: 'Store - New York', code: 'ST-NY', is_warehouse: false },
  { id: 3, name: 'Store - Los Angeles', code: 'ST-LA', is_warehouse: false },
  { id: 4, name: 'Store - Chicago', code: 'ST-CH', is_warehouse: false },
  { id: 5, name: 'Store - Houston', code: 'ST-HO', is_warehouse: false }
]

export const mockSuppliers = [
  { id: 'all', name: 'All Suppliers' },
  { id: 1, name: 'Global Supply Co', code: 'SUP-A' },
  { id: 2, name: 'Pacific Imports', code: 'SUP-B' },
  { id: 3, name: 'Domestic Goods Inc', code: 'SUP-C' }
]
