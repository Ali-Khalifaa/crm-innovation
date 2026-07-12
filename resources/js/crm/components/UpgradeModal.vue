<template>
  <Teleport to="body">
    <div v-if="show" class="modal-overlay" @click.self="$emit('close')">
      <div class="upgrade-modal">
        <div class="modal-header">
          <div>
            <h3>Upgrade Your Plan</h3>
            <p>You've hit a limit on your current <strong>{{ currentPlan?.name }}</strong> plan.</p>
          </div>
          <button class="close-x" @click="$emit('close')">×</button>
        </div>

        <div v-if="loading" class="loading-center"><div class="spinner"></div></div>
        <div v-else class="plans-grid">
          <div
            v-for="plan in plans"
            :key="plan.id"
            class="plan-card"
            :class="{ 'plan-current': plan.id === currentPlan?.id, 'plan-featured': plan.is_featured }"
          >
            <div v-if="plan.is_featured" class="plan-badge">Most Popular</div>
            <h4>{{ plan.name }}</h4>
            <div class="plan-price">${{ plan.monthly_price }}<span>/mo</span></div>
            <ul class="plan-features">
              <li><i class="fas fa-users"></i> {{ plan.max_users === 0 ? 'Unlimited' : plan.max_users }} users</li>
              <li><i class="fas fa-address-book"></i> {{ plan.max_contacts === 0 ? 'Unlimited' : plan.max_contacts.toLocaleString() }} contacts</li>
              <li v-for="f in (plan.features || [])" :key="f">
                <i class="fas fa-check"></i> {{ f.replace(/_/g,' ') }}
              </li>
            </ul>
            <button
              v-if="plan.id !== currentPlan?.id"
              class="btn-upgrade"
              :disabled="requesting"
              @click="requestUpgrade(plan)"
            >
              <span v-if="requesting && selectedPlan?.id === plan.id" class="spinner"></span>
              {{ requesting && selectedPlan?.id === plan.id ? 'Sending...' : 'Request Upgrade' }}
            </button>
            <div v-else class="current-label">Current Plan</div>
          </div>
        </div>

        <p class="upgrade-note">Our team will process your upgrade request within 24 hours.</p>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import { useToast } from '../composables/useToast.js'
import api from '../composables/useApi.js'

defineProps({ show: Boolean })
const emit = defineEmits(['close'])

const store   = useStore()
const toast   = useToast()
const plans   = ref([])
const loading = ref(true)
const requesting   = ref(false)
const selectedPlan = ref(null)

const currentPlan = computed(() => store.getters['auth/plan'])

onMounted(async () => {
  try {
    const { data } = await api.get('/plans')
    plans.value = data.data
  } catch {}
  loading.value = false
})

async function requestUpgrade(plan) {
  selectedPlan.value = plan
  requesting.value = true
  try {
    await api.post('/subscription/request-upgrade', { plan_id: plan.id, message: `Requesting upgrade to ${plan.name}` })
    toast.success('Upgrade request sent! Our team will contact you within 24 hours.')
    emit('close')
  } catch (err) {
    toast.error(err.response?.data?.message || 'Failed to send upgrade request')
  } finally {
    requesting.value = false
    selectedPlan.value = null
  }
}
</script>

<style scoped>
.modal-overlay { position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:3000;display:flex;align-items:center;justify-content:center;padding:24px; }
.upgrade-modal { background:#fff;border-radius:20px;width:100%;max-width:720px;max-height:90vh;overflow-y:auto;padding:32px; }
.modal-header { display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px; }
.modal-header h3 { font-size:22px;font-weight:800;margin:0 0 4px; }
.modal-header p { color:#64748B;font-size:14px;margin:0; }
.close-x { background:none;border:none;font-size:24px;cursor:pointer;color:#94A3B8;line-height:1; }
.plans-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:16px;margin-bottom:20px; }
.plan-card { border:2px solid #E2E8F0;border-radius:14px;padding:20px;position:relative;text-align:center; }
.plan-card.plan-featured { border-color:#2563EB;box-shadow:0 0 0 2px rgba(37,99,235,0.1); }
.plan-card.plan-current { background:#F8FAFC;opacity:0.8; }
.plan-badge { position:absolute;top:-10px;left:50%;transform:translateX(-50%);background:#7C3AED;color:#fff;font-size:10px;font-weight:700;padding:3px 12px;border-radius:20px; }
.plan-card h4 { font-size:16px;font-weight:700;margin:4px 0 8px; }
.plan-price { font-size:28px;font-weight:800;color:#2563EB;margin-bottom:12px; }
.plan-price span { font-size:14px;color:#94A3B8;font-weight:400; }
.plan-features { list-style:none;padding:0;margin:0 0 16px;text-align:left; }
.plan-features li { font-size:13px;color:#475569;padding:4px 0;display:flex;align-items:center;gap:8px; }
.plan-features li i { color:#2563EB;width:14px; }
.btn-upgrade { width:100%;background:#2563EB;color:#fff;border:none;border-radius:8px;padding:10px;font-size:14px;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px; }
.btn-upgrade:hover { background:#1D4ED8; }
.btn-upgrade:disabled { opacity:0.7;cursor:not-allowed; }
.current-label { background:#F1F5F9;color:#64748B;border-radius:8px;padding:10px;font-size:13px;font-weight:600; }
.upgrade-note { text-align:center;color:#94A3B8;font-size:13px;margin:0; }
</style>
