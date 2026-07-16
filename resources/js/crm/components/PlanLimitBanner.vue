<template>
  <div v-if="show && visible" class="plan-limit-banner" :class="isAtLimit ? 'is-danger' : 'is-warning'">
    <i class="bi" :class="isAtLimit ? 'bi-x-circle-fill' : 'bi-exclamation-triangle-fill'"></i>
    <span>{{ message }}</span>
    <button class="upgrade-btn" @click="$emit('upgrade')">
      <i class="bi bi-arrow-up-circle me-1"></i>
      {{ locale === 'ar' ? 'ترقية الباقة' : 'Upgrade Plan' }}
    </button>
    <button class="close-btn" @click="visible = false">×</button>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useStore } from 'vuex'

const props = defineProps({
  current: { type: Number, default: 0 },
  limit:   { type: Number, default: 0 },
  label:   { type: String, default: 'items' },
})
defineEmits(['upgrade'])

const store   = useStore()
const locale  = computed(() => store.state.ui.locale)
const visible = ref(true)

const percentage  = computed(() => props.limit > 0 ? (props.current / props.limit) * 100 : 0)
const isAtLimit   = computed(() => props.limit > 0 && props.current >= props.limit)
const isNearLimit = computed(() => percentage.value >= 80 && !isAtLimit.value)
const show        = computed(() => (isAtLimit.value || isNearLimit.value) && props.limit > 0)

const message = computed(() => {
  if (!show.value) return ''
  if (locale.value === 'ar') {
    if (isAtLimit.value)   return `وصلت للحد الأقصى (${props.current}/${props.limit}) لـ${props.label}. قم بالترقية لإضافة المزيد.`
    if (isNearLimit.value) return `تستخدم ${props.current} من ${props.limit} ${props.label} (${Math.round(percentage.value)}%). قارب على الحد الأقصى.`
  }
  if (isAtLimit.value)   return `You've reached your ${props.label} limit (${props.current}/${props.limit}). Upgrade to add more.`
  if (isNearLimit.value) return `You're using ${props.current} of ${props.limit} ${props.label} (${Math.round(percentage.value)}%). Consider upgrading.`
  return ''
})
</script>

<style scoped>
.plan-limit-banner {
  display: flex; align-items: center; gap: 12px;
  border-radius: 10px; padding: 12px 16px; margin-bottom: 16px;
  font-size: 13.5px;
}
.plan-limit-banner.is-warning { background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; }
.plan-limit-banner.is-danger  { background: #FEF2F2; border: 1px solid #FECACA; color: #991B1B; }
.plan-limit-banner i { flex-shrink: 0; font-size: 15px; }
.plan-limit-banner span { flex: 1; }
.upgrade-btn {
  background: #F59E0B; color: #fff; border: none; border-radius: 6px;
  padding: 5px 12px; font-size: 12px; font-weight: 700; cursor: pointer;
  white-space: nowrap; display: inline-flex; align-items: center;
}
.upgrade-btn:hover { background: #D97706; }
.close-btn { background: none; border: none; font-size: 18px; cursor: pointer; line-height: 1; opacity: .7; }
.close-btn:hover { opacity: 1; }
</style>
