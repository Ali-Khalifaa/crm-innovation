<template>
  <div v-if="visible" class="plan-limit-banner">
    <i class="fas fa-exclamation-triangle"></i>
    <span>{{ message }}</span>
    <button class="upgrade-btn" @click="$emit('upgrade')">
      <i class="fas fa-arrow-up"></i> Upgrade Plan
    </button>
    <button class="close-btn" @click="visible = false">×</button>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
  current: { type: Number, default: 0 },
  limit:   { type: Number, default: 0 },
  label:   { type: String, default: 'items' },
})
defineEmits(['upgrade'])

const visible = ref(true)

const percentage = computed(() => props.limit > 0 ? (props.current / props.limit) * 100 : 0)
const isAtLimit  = computed(() => props.limit > 0 && props.current >= props.limit)
const isNearLimit = computed(() => percentage.value >= 80 && !isAtLimit.value)

const message = computed(() => {
  if (isAtLimit.value)  return `You've reached your ${props.label} limit (${props.current}/${props.limit}). Upgrade to add more.`
  if (isNearLimit.value) return `You're using ${props.current} of ${props.limit} ${props.label} (${Math.round(percentage.value)}%). Upgrade before you hit the limit.`
  return ''
})

const show = computed(() => (isAtLimit.value || isNearLimit.value) && props.limit > 0)
</script>

<style scoped>
.plan-limit-banner {
  display: flex; align-items: center; gap: 12px;
  background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px;
  padding: 12px 16px; margin-bottom: 16px; font-size: 14px; color: #92400E;
}
.plan-limit-banner i { flex-shrink: 0; }
.plan-limit-banner span { flex: 1; }
.upgrade-btn {
  background: #F59E0B; color: #fff; border: none; border-radius: 6px;
  padding: 6px 14px; font-size: 13px; font-weight: 600; cursor: pointer;
  white-space: nowrap;
}
.upgrade-btn:hover { background: #D97706; }
.close-btn { background: none; border: none; font-size: 18px; color: #92400E; cursor: pointer; line-height: 1; }
</style>
