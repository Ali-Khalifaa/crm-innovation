<template>
  <Teleport to="body">
    <div class="toast-container">
      <TransitionGroup name="toast">
        <div
          v-for="t in toasts"
          :key="t.id"
          class="toast-item"
          :class="`toast-${t.type}`"
          @click="remove(t.id)"
        >
          <i :class="iconClass(t.type)"></i>
          <span>{{ t.message }}</span>
        </div>
      </TransitionGroup>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from '../composables/useToast.js'

const { toasts } = useToast()

function remove(id) {
  const idx = toasts.findIndex(t => t.id === id)
  if (idx !== -1) toasts.splice(idx, 1)
}

function iconClass(type) {
  return {
    success: 'fas fa-check-circle',
    error:   'fas fa-times-circle',
    warning: 'fas fa-exclamation-triangle',
    info:    'fas fa-info-circle',
  }[type] || 'fas fa-info-circle'
}
</script>

<style scoped>
.toast-container {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 9999;
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-width: 360px;
}

.toast-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 13px 18px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  box-shadow: 0 4px 20px rgba(0,0,0,0.12);
  border-left: 4px solid transparent;
}
.toast-success { background:#F0FDF4; color:#166534; border-left-color:#22C55E; }
.toast-error   { background:#FEF2F2; color:#991B1B; border-left-color:#EF4444; }
.toast-warning { background:#FFFBEB; color:#92400E; border-left-color:#F59E0B; }
.toast-info    { background:#EFF6FF; color:#1E40AF; border-left-color:#3B82F6; }

/* Transitions */
.toast-enter-active { animation: slide-in 0.3s ease; }
.toast-leave-active { animation: slide-out 0.25s ease forwards; }
@keyframes slide-in  { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:none; } }
@keyframes slide-out { from { opacity:1; transform:none; } to { opacity:0; transform:translateX(40px); } }
</style>
