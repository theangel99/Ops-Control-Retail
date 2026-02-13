<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAppStore } from './stores/app'
import { api } from './api'

const appStore = useAppStore()
const router = useRouter()
const locations = ref([])

onMounted(async () => {
  try {
    const response = await api.getLocations()
    locations.value = [{ id: 'all', name: 'All Locations' }, ...response.data.data]
  } catch (error) {
    console.error('Failed to load locations:', error)
  }
})

const handleRoleChange = (newRole) => {
  appStore.setRole(newRole)
}

const handleLocationChange = (event) => {
  appStore.setLocation(event.target.value)
}
</script>

<template>
  <div class="app-container">
    <header class="app-header">
      <div class="header-left">
        <h1>OpsControl</h1>
        <nav class="nav-menu">
          <router-link to="/" class="nav-link">Dashboard</router-link>
          <router-link to="/inventory" class="nav-link">Inventory Control</router-link>
          <router-link to="/purchase-orders" class="nav-link">Purchase Workflow</router-link>
        </nav>
      </div>
      <div class="header-right">
        <div class="control-group">
          <label>Role:</label>
          <select :value="appStore.role" @change="handleRoleChange($event.target.value)" class="role-selector">
            <option value="Executive">Executive</option>
            <option value="Ops Manager">Ops Manager</option>
          </select>
        </div>
        <div class="control-group">
          <label>Location:</label>
          <select :value="appStore.selectedLocation" @change="handleLocationChange" class="location-selector">
            <option v-for="loc in locations" :key="loc.id" :value="loc.id">
              {{ loc.name }}
            </option>
          </select>
        </div>
      </div>
    </header>

    <main class="app-main">
      <router-view />
    </main>
  </div>
</template>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  background: #f8f9fa;
  color: #333;
  line-height: 1.6;
}

.app-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.app-header {
  background: #fff;
  border-bottom: 1px solid #e5e7eb;
  padding: 1.25rem 2.5rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.header-left {
  display: flex;
  align-items: center;
  gap: 3rem;
}

.header-left h1 {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  letter-spacing: -0.02em;
}

.nav-menu {
  display: flex;
  gap: 2rem;
}

.nav-link {
  text-decoration: none;
  color: #6b7280;
  font-weight: 500;
  font-size: 0.9375rem;
  padding: 0.5rem 0;
  border-bottom: 2px solid transparent;
  transition: all 0.2s ease;
}

.nav-link:hover {
  color: #1f2937;
}

.nav-link.router-link-active {
  color: #1f2937;
  border-bottom-color: #3b82f6;
}

.header-right {
  display: flex;
  gap: 1.5rem;
  align-items: center;
}

.control-group {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.control-group label {
  font-size: 0.875rem;
  color: #6b7280;
  font-weight: 600;
  white-space: nowrap;
}

.role-selector,
.location-selector {
  padding: 0.625rem 1rem;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  background: #fff;
  font-size: 0.875rem;
  color: #1f2937;
  cursor: pointer;
  transition: all 0.2s ease;
  min-width: 140px;
}

.role-selector:hover,
.location-selector:hover {
  border-color: #9ca3af;
}

.role-selector:focus,
.location-selector:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.app-main {
  flex: 1;
  padding: 2.5rem;
  max-width: 1920px;
  margin: 0 auto;
  width: 100%;
}

@media (max-width: 1200px) {
  .app-header {
    padding: 1rem 1.5rem;
  }

  .header-left {
    gap: 2rem;
  }

  .nav-menu {
    gap: 1.5rem;
  }

  .app-main {
    padding: 2rem 1.5rem;
  }
}

@media (max-width: 768px) {
  .app-header {
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }

  .header-left,
  .header-right {
    width: 100%;
    justify-content: space-between;
  }

  .nav-menu {
    gap: 1rem;
  }

  .app-main {
    padding: 1.5rem 1rem;
  }
}
</style>
