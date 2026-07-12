<template>
  <CrmLayout>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start;">

      <!-- Company Info -->
      <div class="crm-card">
        <div class="crm-card-header">
          <span class="crm-card-title">Company Settings</span>
          <button v-if="!editing" @click="startEdit" class="btn-secondary btn-sm">
            <i class="fas fa-edit"></i> Edit
          </button>
        </div>

        <div v-if="!editing">
          <div class="info-row"><span class="info-label">Company Name</span><span class="info-value">{{ tenant?.name }}</span></div>
          <div class="info-row"><span class="info-label">Email</span><span class="info-value">{{ tenant?.email }}</span></div>
          <div class="info-row"><span class="info-label">Phone</span><span class="info-value">{{ tenant?.phone || '—' }}</span></div>
          <div class="info-row"><span class="info-label">Status</span><span class="badge" :class="statusClass(tenant?.status)">{{ tenant?.status }}</span></div>
          <div class="info-row"><span class="info-label">Billing</span><span class="info-value">{{ tenant?.billing_cycle }}</span></div>
          <div v-if="tenant?.trial_ends_at" class="info-row">
            <span class="info-label">Trial Ends</span>
            <span class="info-value">{{ new Date(tenant.trial_ends_at).toLocaleDateString() }}</span>
          </div>
        </div>

        <form v-else @submit.prevent="saveChanges">
          <div class="form-group">
            <label class="form-label">Company Name *</label>
            <input v-model="form.name" class="form-control" required />
          </div>
          <div class="form-group">
            <label class="form-label">Phone</label>
            <input v-model="form.phone" class="form-control" placeholder="+1 555 000 0000" />
          </div>
          <div v-if="error" style="color:#EF4444;font-size:13px;margin-bottom:12px;">{{ error }}</div>
          <div style="display:flex;gap:10px;">
            <button type="submit" class="btn-primary" :disabled="saving">
              <span v-if="saving" class="spinner"></span>
              {{ saving ? 'Saving...' : 'Save Changes' }}
            </button>
            <button type="button" @click="editing = false" class="btn-secondary">Cancel</button>
          </div>
        </form>
      </div>

      <!-- Subscription -->
      <div class="crm-card">
        <div class="crm-card-header">
          <span class="crm-card-title">Current Plan</span>
          <button @click="showUpgrade = true" class="btn-primary btn-sm">
            <i class="fas fa-arrow-up"></i> Upgrade
          </button>
        </div>

        <div style="text-align:center;padding:16px 0 20px;">
          <div style="font-size:36px;font-weight:900;color:#2563EB;">{{ plan?.name }}</div>
          <div style="font-size:20px;font-weight:700;margin:6px 0;">
            ${{ plan?.monthly_price }}<span style="font-size:13px;color:#94A3B8;">/mo</span>
          </div>
          <span class="badge" :class="statusClass(tenant?.status)" style="font-size:13px;padding:4px 14px;">{{ tenant?.status }}</span>
        </div>

        <div style="border-top:1px solid var(--border,#E2E8F0);padding-top:14px;">
          <div class="info-row">
            <span class="info-label">Users</span>
            <span class="info-value">{{ plan?.max_users === 0 ? 'Unlimited' : plan?.max_users }}</span>
          </div>
          <div class="info-row">
            <span class="info-label">Contacts</span>
            <span class="info-value">{{ plan?.max_contacts === 0 ? 'Unlimited' : Number(plan?.max_contacts).toLocaleString() }}</span>
          </div>
          <div v-for="f in (plan?.features || [])" :key="f" style="padding:6px 0;font-size:13px;color:var(--text-muted,#64748B);">
            <i class="fas fa-check" style="color:#10B981;margin-right:6px;"></i>{{ f.replace(/_/g,' ') }}
          </div>
        </div>
      </div>
    </div>

    <UpgradeModal :show="showUpgrade" @close="showUpgrade = false" />
  </CrmLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useStore } from 'vuex'
import CrmLayout from '../../layouts/CrmLayout.vue'
import UpgradeModal from '../../components/UpgradeModal.vue'
import api from '../../composables/useApi.js'
import { useToast } from '../../composables/useToast.js'

const store   = useStore()
const toast   = useToast()
const editing = ref(false)
const saving  = ref(false)
const error   = ref('')
const showUpgrade = ref(false)

const tenant = computed(() => store.getters['auth/tenant'])
const plan   = computed(() => store.getters['auth/plan'])
const form   = ref({ name: '', phone: '' })

function startEdit() {
  form.value = { name: tenant.value?.name || '', phone: tenant.value?.phone || '' }
  editing.value = true
  error.value = ''
}

async function saveChanges() {
  saving.value = true; error.value = ''
  try {
    const { data } = await api.put('/settings/company', form.value)
    store.commit('auth/SET_TENANT', { ...tenant.value, name: form.value.name, phone: form.value.phone })
    editing.value = false
    toast.success('Company settings saved')
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to save'
    toast.error(error.value)
  } finally { saving.value = false }
}

function statusClass(s) {
  return { active: 'badge-success', trial: 'badge-warning', suspended: 'badge-danger', cancelled: 'badge-gray' }[s] || 'badge-gray'
}

onMounted(() => store.dispatch('auth/fetchMe').catch(() => {}))
</script>

<style scoped>
.info-row { display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border,#F1F5F9);font-size:14px; }
.info-row:last-child { border-bottom:none; }
.info-label { color:var(--text-muted,#64748B);font-weight:500; }
.info-value { font-weight:600;color:var(--text,#1E293B); }
</style>
