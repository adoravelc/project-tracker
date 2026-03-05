<script setup lang="ts">
import Sidebar from '@/components/Sidebar.vue'
</script>

<template>
  <div class="layout">
    <Sidebar />

    <main class="main-content">
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
.layout {
  min-height: 100vh;
  background: var(--color-background);
}

.main-content {
  margin-left: 260px;
  min-height: 100vh;
  padding: 2rem;
}
</style>
