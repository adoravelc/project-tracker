<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const handleLogout = async () => {
  await authStore.logout()
  await router.push('/login')
}
</script>

<template>
  <aside class="sidebar">
    <div class="sidebar-top">
      <h1 class="logo">TaskTracker</h1>

      <nav class="menu">
        <RouterLink to="/dashboard" class="menu-link">Dashboard</RouterLink>
        <RouterLink to="/projects" class="menu-link">Project</RouterLink>
        <RouterLink to="/tasks" class="menu-link">Task</RouterLink>
      </nav>
    </div>

    <div class="sidebar-bottom">
      <p class="profile-name">Admin Energeek</p>
      <button type="button" class="logout-button" @click="handleLogout">Logout</button>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  position: fixed;
  left: 0;
  top: 0;
  width: 260px;
  height: 100vh;
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  border-right: 1px solid var(--color-border);
  background: #2b2b2b;
}

.logo {
  margin: 0 0 1rem;
  color: var(--color-primary);
  font-size: 1.5rem;
  font-weight: 700;
}

.menu {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.menu-link {
  display: block;
  padding: 0.65rem 0.8rem;
  border-radius: 8px;
  text-decoration: none;
  color: var(--color-secondary);
}

.menu-link.router-link-active,
.menu-link.router-link-exact-active {
  background: var(--color-primary);
  color: var(--color-secondary);
}

.profile-name {
  margin: 0 0 0.75rem;
  color: var(--color-secondary);
  font-weight: 600;
}

.logout-button {
  width: 100%;
  border: none;
  border-radius: 8px;
  padding: 0.65rem 0.75rem;
  cursor: pointer;
  background: var(--color-primary);
  color: var(--color-secondary);
}
</style>
